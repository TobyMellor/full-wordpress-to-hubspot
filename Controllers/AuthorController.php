<?php

require_once(__DIR__ . '/../Models/Author.php');

class AuthorController {
    private $authors = [];

    public function getAuthors() {
        return $this->authors;
    }

    public function getAuthorIDByLogin($login) {
        foreach ($this->getAuthors() as $author) {
            if ($author->getLogin() === $login) {
                return $author;
            }
        }

        $rawAuthors = json_decode(
            json_encode(
                simplexml_load_file('import_file.xml', 'SimpleXMLElement', LIBXML_NOCDATA)->channel
            ), true
        )['author'];

        foreach ($rawAuthors as $rawAuthor) {
            if ($rawAuthor['author_login'] === $login) {
                $author = $this->createAuthor($rawAuthor['author_login'], $rawAuthor['author_email'], $rawAuthor['author_display_name']);

                array_push($this->authors, $author);

                return $author;
            }
        }

        die('No author exists for a post!');
    }

    public function createAuthor($login, $email, $displayName) {
        $author = new Author;

        $author->setLogin($login);
        $author->setEmail($email);
        $author->setDisplayName($displayName);

        sleep(5); // seems to need time to process on Hubspots end

        return $author->save();
    }
}