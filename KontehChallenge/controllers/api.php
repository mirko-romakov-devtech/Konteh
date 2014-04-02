<?php
$directories = array('../controllers', '../Helpers', '../models', get_include_path());
set_include_path(implode(PATH_SEPARATOR, $directories));

require_once 'models.php';

/*
	INFORM USER TO PASS DATA AS JSON
*/
$data = json_decode($_POST['data'], true);
if(!$data) {
	echo json_encode(Response::error("You must pass your data as JSON"));
	return;
}
	
require_once 'dbhandler.php';

/*
	NO ACTION? BYE
*/
if(!isset($data['action'])) {
	echo json_encode(Response::error("You must provide an action."));
	return;
}

$GLOBALS['loDbHandler'] = new DBHandler();

switch($data['action']){
	case "getCredentials" :
		GetCredentials($data['guid']);
		break;
	case "createServer" :
		CreateServer($data['token'],$data['dataObject']);
		break;
	case "openVNCConnection" :
		OpenVNC($data['token'], $data['dataObject']);
		break;
	case "sendEmail":
		SendEmail($data['params']);
		break;
	case "getVersion":
		GetVersion($data['token']);
		break;
	default :
		echo json_encode(Response::error("You must provide a valid action."));
		return;
}
/*
	API FUNCTIONS
*/
function GetCredentials($guid){
	if(!isset($guid))
	{
		echo json_encode(Response::error("You must send your key."));
		return;
	}
	if(checkGuid($guid)) {
		$response = $GLOBALS['loDbHandler']->generateApiToken($guid);
		if(!$response->success) {
			echo json_encode($response);
			return;
		}
		$data = array('token'=>$response->data, 'DBcredentials' => array('host'=>ConfigParser::DBDATABASE(),'username'=>ConfigParser::DBDUMMYUSER(), 'password'=>ConfigParser::DBDUMMYPASS()));
		if($GLOBALS['loDbHandler']->superLog($response->data, Tasks::GetCredentials))
			echo json_encode(Response::success("You have successfully requested credentials.", $data));
		else 
			echo json_encode(Response::error("Something went wrong. Please try again."));
	}
	else
		echo json_encode(Response::error("Key you provided is not valid."));
}

function CreateServer($apiToken,$createServerRequest)
{
	if(!isset($apiToken))
	{
		echo json_encode(Response::error("You must send a token."));
		return;
	}
	if(checkToken($apiToken)) {
		if($GLOBALS['loDbHandler']->validateStep($apiToken,Tasks::CreateServer)){
			echo json_encode(Response::error("You must first request credentials."));
			return;
		}
		$cpuMax = 4;
		$ramMax = 4096;
		$hddMax = 200;
		if(!isset($createServerRequest['cpu']) || !is_numeric($createServerRequest['cpu']) || trim($createServerRequest['cpu']) == "" ||
			!isset($createServerRequest['ram']) || !is_numeric($createServerRequest['ram']) || trim($createServerRequest['ram']) == "" ||
			!isset($createServerRequest['hdd']) || !is_numeric($createServerRequest['hdd']) || trim($createServerRequest['hdd']) == "" ||
			!isset($createServerRequest['name']) || trim($createServerRequest['name']) == "") {
			echo json_encode(Response::error("You must provide following data: cpu, ram, hdd, server name."));
			return;
		}
		if($createServerRequest['cpu'] > $cpuMax ||	$createServerRequest['ram'] > $ramMax || $createServerRequest['hdd'] > $hddMax){
			echo json_encode(Response::error("You can create server that has maximum ".$cpuMax." CPUs, ".$ramMax."MB of RAM and ".$hddMax."GB HDD"));
			return;
		}
		$serverInfo = new ServerInfo($GLOBALS['loDbHandler']->getCustomerName($apiToken)->data, $createServerRequest['cpu'],
				 $createServerRequest['ram'], $createServerRequest['hdd'], $createServerRequest['name']);
			
		if($GLOBALS['loDbHandler']->superLog($apiToken, Tasks::CreateServer))
			echo json_encode(Response::success("You have successfully created server named ".$createServerRequest['name'], array("server" => $serverInfo)));
		else
			echo json_encode(Response::error("Something went wrong. Please try again."));
	}
	else 
		echo json_encode(Response::error("Token you provided is not valid."));
}

function OpenVNC($apiToken, $openVNCRequest)
{
	if(!isset($apiToken)) {
		echo json_encode(Response::error("You must send a token."));
		return;
	}
	if(checkToken($apiToken)) {
		if($GLOBALS['loDbHandler']->validateStep($apiToken,Tasks::OpenVNC)){
			echo json_encode(Response::error("You must first create server."));
			return;
		}
		if(!checkVNCRequest($openVNCRequest, $apiToken))
			return;
		$data = array('guid' => $GLOBALS['loDbHandler']->getGuidFromToken($apiToken), 'supersecretkey' => '01470a4f4b9897959bc5baf5c08cd5e2');
	
		$url = 'http://37.220.108.91/api.php';
	
		// Get cURL resource
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $data
		));
		$url = curl_exec($curl);
		curl_close($curl);
		
		if($url=="LIAR LIAR PANTS ON FIRE!"){
			//ovo se desava kada je fajl vec kreiran na VM
			//echo json_encode(Response::error("You have already openned VNC."));
			//return;
		}
		$key = $GLOBALS['loDbHandler']->activationKey($apiToken);
		if($GLOBALS['loDbHandler']->superLog($apiToken, Tasks::OpenVNC)) {
			$linkModel = new LinkModel();
			$linkModel->Action = LinkAction::OPENVNC;
			$linkModel->GUID = $data['guid'];
			$linkModel->Used = 0;
			$encryptor = new EncryptionHelper(ConfigParser::DBHOST(), ConfigParser::DBDATABASE(), ConfigParser::DBUSERNAME(), ConfigParser::DBPASSWORD());
			$kobaja = $encryptor->encryptObject($linkModel);
			$GLOBALS['loDbHandler']->logKobaja($linkModel, $kobaja);
			
			echo json_encode(Response::success("You have successfully opened VNC.", array("url" => "http://37.220.108.91/terminal.php?key=".$key)));
		} else {
			echo json_encode(Response::error("Something went wrong. Please try again."));
		}
	} 
	else
		echo json_encode(Response::error("Token you provided is not valid."));
}

function GetVersion($apiToken) {
	$responseObject = new ApiResponse();
	if (checkToken($apiToken)) {
		$apiDetails = (object) array("name"=>"Devtech challenge","version"=>"1.0");
		$responseObject->data = $apiDetails;
	} else {
		$responseObject->success = false;
		$responseObject->message = "Invalid token";
	}
	echo json_encode($responseObject);
}


function SendEmail($params){
	
	$candidateParams = $GLOBALS['loDbHandler']->checkEmail($params['guid']);
	if($params['email'] == $candidateParams['email'])
	{
		//sent mail function
		$emailParams = array($candidateParams['guid'], $candidateParams['email'], $candidateParams['firstname'], $candidateParams['lastname']);
		$mail = new EmailController();
		$mail->send($emailParams);
		echo json_encode(Response::success("You have finished Challenge successfully. Congratulation!", $emailParams));
	}
	else
	 	echo json_encode(Response::error("Your email is not valid!"));
}

/*
	VALIDATION FUNCTIONS
*/
function checkGuid($guid) {
	return $GLOBALS['loDbHandler']->checkGuid($guid);
}


function checkToken($token) {
	return $GLOBALS['loDbHandler']->checkToken($token);
}

function checkVNCRequest($vncRequest, $token)
{
	if(!isset($vncRequest['serverID']) || !isset($vncRequest['username']) || !isset($vncRequest['password']))
	{
		echo json_encode(Response::error("You must send VNC request data."));
		return false;
	}   
	
	$credentials = new CandidateCredentials($token, $vncRequest['username'], $vncRequest['password']);
	
	$response = $GLOBALS['loDbHandler']->checkVNCCredentials($credentials);

	if(!$response->success)
	{
		echo json_encode($response);
		return false;
	}
	return true;
}

?>