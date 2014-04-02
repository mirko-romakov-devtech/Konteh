<?php
require_once 'ConfigParser.php';
require_once 'EncryptionHelper.php';

//$model = new DBHandler();
//$model->generateApiToken("asdff");
//$model->asdfSql("DELETE FROM candidatecredentials WHERE api_token='c3ff70167dc2be59151ae18dec4a51a5'");
//$model->asdfSql("TRUNCATE candidates");
//$model->tranketuj();
//$model->viewTable("progresslog");
//$model->asdfSql("show tables");
//$model->viewTable("candidatecredentials");
//$model->asdfSql("SELECT firstname,lastname FROM candidates as ca JOIN candidatecredentials as cc on ca.candidate_id=cc.candidate_id WHERE api_token='46a77beb93bd2bfd7b7b8463b5fb071b'");
//echo '<br>';
//echo $model->checkToken("c3ff70167dc2be59151ae18dec4a51a5") ? "ima" : "nema";
//$model->asdfSql("DELETE FROM candidatecredentials WHERE candidate_id=asdf");
/*email
 firstname
lastname
notes*/
/*foreach($model->_db->query("show databases") as $table) {
 print_r($table);
//echo $table[0];
echo '<br><br>';
$sql = "DESCRIBE ".$table[0];
foreach($model->_db->query($sql) as $row){
echo $row['Field'];
echo '<br>';
}
echo '<br><br><br><br>';
}*/

/*
 DATABASE
 */
class DBHandler {
	private $_db;

	public function __construct(){
		try {
			$connectionString = 'mysql:dbname=%s;host=%s';
			$this->_db = new PDO(sprintf($connectionString, ConfigParser::DBDATABASE(), ConfigParser::DBHOST()), ConfigParser::DBUSERNAME(), ConfigParser::DBPASSWORD());
			$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e) {
			//return Response::error("There is a problem with database. Please try again later");
			echo $e->getMessage();
		}
	}

	private function tokenExists($guid) {
		$sql = "SELECT * FROM candidatecredentials WHERE candidate_id=?";
		$lsQuery = $this->_db->prepare($sql);
		$lsQuery->execute(array($guid));
		$result = $lsQuery->fetchAll(PDO::FETCH_ASSOC);
		return $result!=null ? true : false;
	}

	private function getUserIP() {
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];

		if(filter_var($client, FILTER_VALIDATE_IP))	{
			$ip = $client;
		} elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}
		return $ip;
	}

	public function checkGuid($guid){
		$sql = "SELECT * FROM candidates WHERE candidate_id=?";
		$lsQuery = $this->_db->prepare($sql);
		$lsQuery->execute(array($guid));
		$result = $lsQuery->fetch(PDO::FETCH_ASSOC);
		return $result!=null ? true : false;
	}

	public function generateApiToken($guid) {
		if($this->tokenExists($guid))
		{
			$sql = "SELECT api_token FROM candidatecredentials WHERE candidate_id=?";
			$lsQuery = $this->_db->prepare($sql);
			$lsQuery->execute(array($guid));
			$result = $lsQuery->fetch(PDO::FETCH_ASSOC);
			if($result)
				return Response::success("Token was already requested.",$result['api_token']);
			return Response::error("Token cannot be retrieved at the moment. Please try again later.");
		}
		$sql = "INSERT into candidatecredentials (api_token,candidate_id) VALUES (?, ?)";
		$apiToken = $guid;
		for($i=0;$i<15;$i++)
			$apiToken = md5($apiToken);
		$lsQuery = $this->_db->prepare($sql);
		$count = $lsQuery->execute(array($apiToken,$guid));
		if($count>0)
			return Response::success("Token generated!",$apiToken);
		return Response::error("Token cannot be generated at the moment. Please try again later.");
	}

	public function checkToken($token) {
		$sql = "SELECT * FROM candidatecredentials WHERE api_token=?";
		$lsQuery = $this->_db->prepare($sql);
		$lsQuery->execute(array($token));
		$result = $lsQuery->fetch(PDO::FETCH_ASSOC);
		return $result!=null ? true : false;
	}

	public function checkVNCCredentials($credentials) {
		$sql = "SELECT * FROM candidatecredentials WHERE api_token=? AND vnc_username=? AND vnc_password=?";
		$lsQuery = $this->_db->prepare($sql);
		$lsQuery->execute(array($credentials->api_token, $credentials->vnc_username, $credentials->vnc_password));
		$result = $lsQuery->fetch(PDO::FETCH_ASSOC);
		if($result)
			return Response::success("Credentials are valid");
		return Response::error("Credentials you provided are not valid.");
	}

	public function getCustomerName($apiToken){
		$sql = "SELECT firstname,lastname FROM candidates ca JOIN candidatecredentials cc on ca.candidate_id=cc.candidate_id WHERE api_token=?";
		$lsQuery = $this->_db->prepare($sql);
		$lsQuery->execute(array($apiToken));
		$result = $lsQuery->fetch(PDO::FETCH_ASSOC);
		$username = $result['firstname']." ".$result['lastname'];
		if(count($result)>0)
			return Response::success("Customer name generated.", $username);
		return Response::error("Customer name could not be generated at the moment.");
	}

	public function logProgress($guid, $task) {
		$query = "INSERT INTO progresslog (candidate_id, timestamp, task_id, ip) VALUES (?, ?, ?, ?)";
		$response = "";
		try {
			$ip = $this->getUserIP();
			$statement = $this->_db->prepare($query);
			$count = $statement->execute(array($guid, time(), $task, $ip));
			if ($count <= 0) {
				throw new Exception("Action cannot be logged at the moment: ".$this->_db->errorInfo());
			}
			$response = Response::success("Action logged.");
		} catch (Exception $ex) {
			$response = Response::error($ex->getMessage());
		}
		return $response;
	}

	public function superLog($apiToken,$task){
		$response = "";
		$guid = $this->getGuidFromToken($apiToken);
		if(!$guid) {
			$response = Response::error("Token you provided is not valid.");
		} else {
			$response = $this->logProgress($guid, $task);
		}
		return $response;
	}

	public function validateStep($apiToken,$aiTask) {
		$guid = $this->getGuidFromToken($apiToken);
		$task;
		switch ($aiTask) {
			case Tasks::CreateServer:
				$task = Tasks::GetCredentials;
				break;
			case Tasks::OpenVNC:
				$task = Tasks::CreateServer;
				break;
		}
		$sql = "SELECT * FROM progresslog WHERE candidate_id=? AND task_id=?";
		$lsQuery = $this->_db->prepare($sql);
		$lsQuery->execute(array($guid,$task));
		$result = $lsQuery->fetch(PDO::FETCH_ASSOC);
		return $result==null ? true : false;
	}

	public function isItLogged($guid, $task){
		return false;
		$sql = "SELECT * FROM progresslog WHERE candidate_id=? AND task_id=?";
		$lsQuery = $this->_db->prepare($sql);
		$lsQuery->execute(array($guid,$task));
		$result = $lsQuery->fetch(PDO::FETCH_ASSOC);
		return $result==null ? true : false;
	}

	public function getGuidFromToken($apiToken){
		$sql = "SELECT candidate_id FROM candidatecredentials WHERE api_token=?";
		$lsQuery = $this->_db->prepare($sql);
		$lsQuery->execute(array($apiToken));
		$result = $lsQuery->fetch(PDO::FETCH_ASSOC);
		if(count($result)>0)
			return $result['candidate_id'];
		return false;
	}

	public function activationKey($apiToken) {
		$guid = $this->getGuidFromToken($apiToken);
		$sql = "SELECT action, used FROM activation WHERE candidate_id=?";
		$lsQuery = $this->_db->prepare($sql);
		$lsQuery->execute(array($guid));
		$result = $lsQuery->fetch(PDO::FETCH_ASSOC);
		if(count($result)>0) {
			$aoLink = new LinkModel();
			$aoLink->Action = $result['action'];
			$aoLink->Used = $result['used'];
			$aoLink->GUID = $guid;
			$encryption = new EncryptionHelper(ConfigParser::DBHOST(), ConfigParser::DBDATABASE(), ConfigParser::DBUSERNAME(), ConfigParser::DBPASSWORD());
			return $encryption->encryptObject($aoLink);
		}
		return false;
	}

	public function checkEmail($guid){
		$sql = "SELECT * FROM candidates WHERE candidate_id = ? ";
		$lsQuery = $this->_db->prepare($sql);
		$lsQuery->execute(array($guid));
		$result = $lsQuery->fetch(PDO::FETCH_ASSOC);
		if(count($result)>0)
			return $result;
		return false;
	}

	public function logKobaja(LinkModel $linkModel, $kobaja) {
		$query = "INSERT INTO activation (candidate_id, action, encrypted_value, used) VALUES (?, ?, ?, ?)";
		$response = "";
		try {
			$checkStatement = $this->_db->prepare("SELECT * FROM activation WHERE candidate_id = ? AND action = ?");
			$checkStatement->execute(array($linkModel->GUID, $linkModel->Action));
			if ($checkStatement->rowCount() > 0) {
				throw new Exception("Encrypted value already logged");
			}
			
			$statement = $this->_db->prepare($query);
			$result = $statement->execute(array($linkModel->GUID, $linkModel->Action, $kobaja, $linkModel->Used));
			if (count($result) <= 0) {
				throw new Exception("Couldn't log encrypted value to database: ".$this->_db->errorInfo());
			}
			$response = Response::success("Encrypted value successfully logged.");
		} catch (Exception $ex) {
			$response = Response::error($ex->getMessage());
		}
		return $response;
	}
}

?>