<?php

require_once('APIController.php');
require_once('FileController.php');
require_once('TopicController.php');

use Carbon\Carbon;

class BlogPostController {
    private $blogPosts;

    private $APIController;
    private $fileController;
    private $topicController;

    public function __construct($rawBlogPosts, $featuredImages) {
        include(__DIR__ . '/../private/details.php');

        $blogID = $privateDetails['blog_id'];

        $authorController       = new AuthorController;
        $this->APIController    = new APIController;
        $this->fileController   = new FileController;
        $this->topicController  = new TopicController;

        foreach ($rawBlogPosts as $rawBlogPost) {
            $publishDate        = Carbon::parse($rawBlogPost['post_date'])->timestamp;
            $publishImmediately = $rawBlogPost['status'] === 'publish';

            $postFeaturedImage = $this->getFeaturedImage($rawBlogPost, $featuredImages);

            $blogAuthorID = $authorController->getAuthorIDByLogin($rawBlogPost['creator'])->getID();

            if (isset($rawBlogPost['postmeta'])) {
                foreach ($rawBlogPost['postmeta'] as $meta) {
                    if ($meta['meta_key'] === '_yoast_wpseo_metadesc') {
                        $metaDescription = $meta['meta_value'];
                    }
                }

                if (!isset($metaDescription)) {
                    $metaDescription = strlen($rawBlogPost['content']) > 50 ? substr($in, 0, 150) . '[...]' : $rawBlogPost['content']; // truncate (max of 160 chars)
                }
            }

            $topicIDs = $this->getBlogPostTopicIDs($rawBlogPost);

            $rawBlogPost['content'] = nl2br($this->replaceFileReferences($rawBlogPost['content'])); // replace \n with <br />, and upload any inline images to hubspot

            $responseJSON = $this->APIController->makeRequest('/content/api/v2/blog-posts', [
                'json' => [
                    'content_group_id'    => $blogID,
                    'blog_author_id'      => $blogAuthorID,
                    'topic_ids'           => $topicIDs,
                    'featured_image'      => $postFeaturedImage,
                    'use_featured_image'  => $postFeaturedImage !== null,
                    'name'                => $rawBlogPost['title'],
                    'post_body'           => $rawBlogPost['content'],
                    'publish_date'        => $publishDate,
                    'publish_immediately' => $publishImmediately,
                    'slug'                => trim(parse_url($rawBlogPost['link'])['path'], '/'),
                    'meta_description'    => $metaDescription
                ]
            ]);

            if ($publishImmediately) {
                $this->APIController->makeRequest('/content/api/v2/blog-posts/' . $responseJSON->id . '/publish-action', [
                    'json' => [
                        'blog_post_id' => $responseJSON->id,
                        'action'       => 'schedule-publish'
                    ]
                ]);
            }

            sleep(0.2); // prevent rate limit of 10 requests per second
        }
    }

    public function getBlogPosts() {
        return $this->blogPosts;
    }

    private function getFeaturedImage($rawBlogPost, $featuredImages) {
        foreach ($rawBlogPost['postmeta'] as $meta) {
            if ($meta['meta_key'] === '_thumbnail_id') {
                foreach ($featuredImages as $featuredImage) {
                    if ($featuredImage['post_id'] === $meta['meta_value']) {
                        return $this->fileController->uploadFile($featuredImage['attachment_url']);
                    }
                }

                break;
            }
        }

        foreach ($featuredImages as $featuredImage) {
            if ($featuredImage['post_parent'] === $rawBlogPost['post_id']) {
                return $this->fileController->uploadFile($featuredImage['attachment_url']);
            }
        }

        return null;
    }

    private function getBlogPostTopicIDs($rawBlogPost) {
        $blogPostTopicsIDs = []; // array of HubSpot Topic IDs this post has
        $rawBlogPostTopics = $rawBlogPost['category'];

        if (sizeOf($rawBlogPostTopics) === 1) { // if there's only 1 topic, XML doesn't return a multi-dimensional array
            $rawBlogPostTopics = [$rawBlogPostTopics];
        }

        foreach ($rawBlogPostTopics as $rawBlogPostTopic) {
            array_push($blogPostTopicsIDs, $this->topicController->getTopic($rawBlogPostTopic)->getID());
        }

        return $blogPostTopicsIDs;
    }

    private function replaceFileReferences($html) {
        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $tags = $doc->getElementsByTagName('img');

        foreach ($tags as $tag) {
            $src = $tag->getAttribute('src');

            if ($src !== null) {
                $absoluteUrl = $src;

                if (filter_var($src, FILTER_VALIDATE_URL) === false) {
                    $absoluteUrl = 'http://blimeycreative.co.uk' . $src;
                }

                $html = str_replace($src, $this->fileController->uploadFile($absoluteUrl), $html);
            }
        }

        return $html;
    }
}