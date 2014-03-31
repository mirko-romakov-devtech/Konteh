<?php
include 'models.php';

/*
	INFORM USER TO PASS DATA AS JSON
*/
$data = json_decode(file_get_contents('php://input'), true);
if(count($_REQUEST)>0 || !$data){
	echo json_encode(Response::error("You must pass your data as JSON"));
	return;
}
if($_SERVER['REMOTE_ADDR'] !='178.222.228.186')
	return;
	

include 'dbhandler.php';

/*
	NO ACTION? BYE
*/
if(!isset($data['action']))
{
	echo json_encode(Response::error("You must provide an action."));
	return;
}

$GLOBALS['loDbHandler'] = new DBHandler();

switch($data['action']){
	case "getCredentials" :
		GetCredentials($data['key']);
		break;
	case "createServer" :
		CreateServer($data['token'],$data['dataObject']);
		break;
	case "openVNCConnection" :
		OpenVNC($data['token'], $data['dataObject']);
		break;
	default :
		echo json_encode(Response::error("You must provide a valid action."));
		return;
}
/*
	API FUNCTIONS
*/
function GetCredentials($guid){
	if(checkGuid($guid))
	{
		if($GLOBALS['loDbHandler']->isItLogged($guid,Tasks::GetCredentials)){
			echo json_encode(Response::error("You have already requested credentials."));
			return;
		}
		$response = $GLOBALS['loDbHandler']->generateApiToken($guid);
		if(!$response->success) {
			echo json_encode($response);
			return;
		}
		$data = array('token'=>$response->data, 'DBcredentials' => array('host'=>'37.220.108.88','username'=>'konteh_user', 'password'=>'&Ux7`kUQ!xy(t{~'));
		if($GLOBALS['loDbHandler']->superLog($response->data, Tasks::GetCredentials))
		{
			echo json_encode(Response::success("You have successfully requested credentials.", $data));
		}
	}
}

function CreateServer($apiToken,$createServerRequest)
{
	if(!checkToken($apiToken))
		return;
	if($GLOBALS['loDbHandler']->isItLogged($GLOBALS['loDbHandler']->getGuidFromToken($apiToken),Tasks::CreateServer)){
		echo json_encode(Response::error("You have already created server."));
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
	$file = 'server.json';
	$server = json_decode(file_get_contents($file), true);
	$server['cpu'] = $createServerRequest['cpu'];
	$server['ram'] = $createServerRequest['ram'];
	$server['disks'][0]['size'] = $createServerRequest['hdd'];
	$server['resourceName'] = $createServerRequest['name'];

	
	$username = $GLOBALS['loDbHandler']->getCustomerName($apiToken)->data;
	if($username)
		$server['customerName'] = $username;
		
	if($GLOBALS['loDbHandler']->superLog($apiToken, Tasks::CreateServer))
		echo json_encode(Response::success("You have successfully created server named ".$createServerRequest['name'], array("server" => $server)));
	return;
}

function OpenVNC($apiToken, $openVNCRequest)
{
	if(!checkToken($apiToken) || !checkVNCRequest($openVNCRequest))
		return;
	if($GLOBALS['loDbHandler']->isItLogged($GLOBALS['loDbHandler']->getGuidFromToken($apiToken),Tasks::OpenVNC)){
		echo json_encode(Response::error("You have already openned VNC connection."));
		return;
	}
	$data = array('guid' => $GLOBALS['loDbHandler']->getGuidFromToken($apiToken), 'supersecretkey' => '01470a4f4b9897959bc5baf5c08cd5e2');

	$url = 'http://37.220.108.91/index.php';

	// Get cURL resource
	$curl = curl_init();
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $data
	));
	// Send the request & save response to $resp
	$url = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);
	if($url=="LIAR LIAR PANTS ON FIRE!"){
		echo json_encode(Response::error("You have already openned VNC."));
		return;
	}
	if($GLOBALS['loDbHandler']->superLog($apiToken, Tasks::OpenVNC))
		echo json_encode(Response::success("You have successfully opened VNC.", array("url" => "http://37.220.108.91/terminal.html")));
	return;
}

/*
	VALIDATION FUNCTIONS
*/
function checkGuid($guid)
{
	if(!isset($guid))
	{
		echo json_encode(Response::error("You must send your key."));
		return false;
	}
	$response = $GLOBALS['loDbHandler']->checkGuid($guid);
	if($response->success)
		return true;
	else
	{
		echo json_encode($response);
		return false;
	}	
}


function checkToken($token)
{
	if(!isset($token))
	{
		echo json_encode(Response::error("You must send a token."));
		return false;
	}

	$response = $GLOBALS['loDbHandler']->checkToken($token);

	if($response->success)
	{
		return true;
	}
	echo json_encode($response);
	return false;
}

function checkVNCRequest($vncRequest)
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