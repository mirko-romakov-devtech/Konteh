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

class Errors {
	const ActivationUsed = 1;
	const KeyNotFound = 2;
	const GuidNotFound = 3;

	public static $ErrorsArray = array(
			'1' => 'You already used this link.',
			'2' => 'Please provide valid key.',
			'3' => 'GUID not found in encrypted value.'
	); 
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

class ServerInfo {
	public $resourceType = "SERVER";
	public $resourceCreateDate = "2014-03-26T14:52:03Z";
	public $resourceName;
	public $resourceState = "ACTIVE";
	public $resourceUUID = "3c39b7f8-1b01-3ff9-9357-0f045336aa44";
	public $customerName;
	public $productOfferName = "Standard Server";
	public $cpu;
	public $ram;
	public $status = "RUNNING";
	public $disks;
	
	public function __construct($asCustomerName,$aiCpu,$aiRam,$aiHdd,$asServerName){
		$this->cpu = $aiCpu;
		$this->ram = $aiRam;
		$this->customerName = $asCustomerName;
		$this->resourceName = $asServerName;
		$this->disks = array(new Disk($asCustomerName, $aiHdd, $asServerName));
	}
}

class Disk {
	public $resourceType = "DISK";
	public $resourceCreateDate = "2014-03-26T14:52:03Z";
	public $resourceName = "Disk 1: saucy-admin";
	public $resourceState = "ACTIVE";
	public $resourceUUID = "ade6ee97-d324-3b6a-9619-889c7097c435";
	public $customerName;
	public $productOfferName = "Standard Disk";
	public $size;
	public $status = "ATTACHED_TO_SERVER";
	public $index = 1;
	public $serverName;
	
	public function __construct($asCustomerName, $aiHdd,$asServerName){
		$this->size = $aiHdd;
		$this->customerName = $asCustomerName;
		$this->serverName = $asServerName;
	}
}
/*
 RESPONSE CLASSES
*/
class ApiResponse {
	public $success;
	public $message;
	public $data;

	public function __construct() {
		$this->success = true;
	}
}

class Response {
	public static function error($asMessage) {
		$response = new ApiResponse();
		$response->success = false;
		$response->message = $asMessage;
		return $response;
	}
	
	public static function success($asMessage, $asData)	{
		$response = new ApiResponse();
		$response->success = true;
		$response->message = $asMessage;
		$response->data = $asData;

		return $response;
	}
}

?>