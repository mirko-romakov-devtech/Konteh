<?php
/*
	TASKS ENUM
*/
abstract class Tasks {
    const GetCredentials = 1;
    const StartPage = 2;
	const CreateServer = 3;
	const FindVNCCredentials = 4;
	const OpenVNC = 5;
	const SSHConnect = 6;
	const FindActivationLink = 7;
}

class CandidateCredentials {
	public $candidatecredentials_id;
	public $api_token;
	public $vnc_username;
	public $vnc_password;
	
	public function __construct($asApiToken,$asVNCUsername,$asVNCPassword) {
		$this->api_token = $asApiToken;
		$this->vnc_username = $asVNCUsername;
		$this->vnc_password = $asVNCPassword;
	}
}

class ApiResponse {
	public $success;
	public $message;
	public $data;

	public function __construct() {
		$this->success = true;
	}
}
/*
	RESPONSE CLASSES
*/
class Response {
	public static function error($asMessage)
	{
		$response = new ApiResponse();
		$response->success = false;
		$response->message = $asMessage;
		return $response;
	}
	
	public static function success($asMessage, $asData)
	{
		$response = new ApiResponse();
		$response->success = true;
		$response->message = $asMessage;
		$response->data = $asData;
			
		return $response;
	}
}

?>