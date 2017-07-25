<?php

use GuzzleHttp\Client;

class APIController {
	private $client;
	private $privateDetails;

	public function __construct() {
		include(__DIR__ . '/../private/details.php');

		$this->client 	      = new Client;
		$this->privateDetails = $privateDetails;
	}

	public function makeRequest($endpoint, $params) {
	    try {
	        $response = $this->client->request(
	            'POST',
	            'https://api.hubapi.com' . $endpoint . '?hapikey=' . $this->privateDetails['hapikey'],
	            $params
	        )->getBody()->getContents();
	    } catch (Exception $e) {
	        var_dump($e->getResponse()->getBody()->getContents(), $endpoint, $params); die('An Error Occurred.');
	    }

	    return json_decode($response);
	}
}