<?php
abstract class Tasks {
	const GetCredentials = 1;
	const StartPage = 2;
	const CreateServer = 3;
	const FindVNCCredentials = 4;
	const OpenVNC = 5;
	const SSHConnect = 6;
	const FindActivationLink = 7;
}

class DatabaseHandler {

	/**
	 * 
	 * @var PDO
	 */
	private $pdoInstance;
	private static $instance;
	private static $connectionString = 'mysql:dbname=%s;host=%s';
	
	private function __construct() {
	}
	
	public static function WithDefaultConfig() {
		if (!isset(self::$instance)) {
			self::$instance = new DatabaseHandler();
			self::$instance->pdoInstance = new PDO(sprintf(self::$connectionString, DB_NAME, DB_HOST), DB_USER, DB_PASS);
		}
		return self::$instance;
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
	
	
	public static function WithCustomConfig($dbHost, $dbDatabaseName, $dbUsername, $dbPassword) {
		if (!isset(self::$instance)) {
			self::$instance = new DatabaseHandler();
		}
		self::$instance->pdoInstance = new PDO(sprintf(self::$connectionString, $dbDatabaseName, $dbHost), $dbUsername, $dbPassword);
		return self::$instance;
	}
	
	public function insertActivation(LinkModel $model, $encrypted) {
		$checkStatement = $this->pdoInstance->prepare("SELECT * FROM konteh.activation WHERE candidate_id = ? AND action = ?");
		$result = $checkStatement->execute(array($model->GUID, $model->Action));
		if ($checkStatement->rowCount() == 0) {
			$statement = $this->pdoInstance->prepare("INSERT INTO konteh.activation (candidate_id, action, encrypted_value, used) VALUES (?, ?, ?, ?)");
			$statement->execute(array($model->GUID, $model->Action, $encrypted, $model->Used));
		}
	}

	public function getCandidateVNCDetails($guid){
		$statement = $this->pdoInstance->prepare("SELECT * FROM konteh.candidatecredentials WHERE candidate_id = ?");
		$executedQuery = $statement->execute(array($guid));
		if(!$executedQuery){
			return false;
		}

		$dbResponse = $statement->fetchAll(PDO::FETCH_ASSOC);
		if(empty($dbResponse)){
			return false;
		} else {
			return array("vnc_username"=>$dbResponse[0]['vnc_username'], "vnc_password"=>$dbResponse[0]['vnc_password']);
		}
	}
	
	public function logProgress($guid, $task) {
		$query = "INSERT INTO progresslog (candidate_id, timestamp, task_id, ip) VALUES (?, ?, ?, ?)";
		$response = "";
		try {
			$ip = $this->getUserIP();
			$statement = $this->pdoInstance->prepare($query);
			$count = $statement->execute(array($guid, time(), $task, $ip));
			if (!$count) {
				throw new Exception("Action cannot be logged at the moment: ".$this->_db->errorInfo());
			}
		} catch (Exception $ex) {
			$response = $ex->getMessage();
		}
		return $response;
	}
}