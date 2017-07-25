<?php

require_once(__DIR__ . '/../Controllers/APIController.php');

class Author {
	private $ID; // value undefined until save()
	private $login;
	private $email;
	private $displayName;

    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = $ID;

        return $this;
    }

    public function getLogin() {
        return $this->login;
    }

    public function setLogin($login) {
        $this->login = $login;

        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    public function getDisplayName() {
        return $this->displayName;
    }

    public function setDisplayName($displayName) {
        $this->displayName = $displayName;

        return $this;
    }

    public function save() {
		$APIController = new APIController;

		$responseJSON = $APIController->makeRequest('/blogs/v3/blog-authors', [
            'json' => [
	            'email'    => $this->getEmail(),
	            'fullName' => $this->getDisplayName()
            ]
        ]);

        $this->setID($responseJSON->id);

		return $this;
    }
}