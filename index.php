<?php

require __DIR__ . '/vendor/autoload.php';

require_once('Controllers/BlogPostController.php');
require_once('Controllers/AuthorController.php');
require_once('Controllers/TopicController.php');

// Make sure you
// 1. Remove prefixes in tags (x:) from import_file.xml and from import_media_file_xml (e.g. wp: dc: and :encoded)
// 2. Fill in the correct details in private/details.php

$rawBlogPosts = json_decode(
    json_encode(
        simplexml_load_file('import_file.xml', 'SimpleXMLElement', LIBXML_NOCDATA)->channel
    ), true
)['item'];

$featuredImages = json_decode(
    json_encode(
        simplexml_load_file('import_meta_file.xml', 'SimpleXMLElement', LIBXML_NOCDATA)->channel
    ), true
)['item'];

$blogPostController = new BlogPostController($rawBlogPosts, $featuredImages);