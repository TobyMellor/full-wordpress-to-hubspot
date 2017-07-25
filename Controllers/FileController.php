<?php

require_once('APIController.php');

class FileController {
	public function uploadFile($url) {
		$file     = file_get_contents($url);
        $fileName = basename($url);

        if ($file !== null) {
	        $APIController = new APIController;

	        $responseJSON = $APIController->makeRequest('/filemanager/api/v2/files', [
	            'multipart' => [
	                'files' => [
	                    'name'     => 'image1',
	                    'contents' => $file,
	                    'filename' => $fileName
	                ],
	            ]
	        ]);

	        return $responseJSON->objects[0]->friendly_url; // the new url where the image is hosted
    	}

    	return $url;
	}
}