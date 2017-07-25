<?php

require_once(__DIR__ . '/../Controllers/APIController.php');

class Topic {
	private $ID; // value undefined until save()
	private $name;

	public function getID() {
		return $this->ID;
	}

	public function setID($ID) {
		$this->ID = $ID;

		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	public function save() {
		$APIController = new APIController;

		$responseJSON = $APIController->makeRequest('/blogs/v3/topics', [
            'json' => [
                'name' => $this->getName()
            ]
        ]);

        $this->setID($responseJSON->id);

		return $this;
	}
}