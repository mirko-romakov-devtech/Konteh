<?php

require_once("/models/candidate_model.php");
require_once("/include/config.php");

class DatabaseController{

	private $databaseHandler;

	public function __construct() {
		$this->databaseHandler = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASS);
	}

	public function insertData($params){
		$sql = "INSERT INTO `candidates` (candidate_id, email, firstname, lastname, notes) VALUES (?, ?, ?, ?, ?)";
		$statement = $this->databaseHandler->prepare($sql);
		$statement->execute($params);
	}

	public function selectData(){
		$result = "";
		$query = $this->databaseHandler->query("SELECT c.candidate_id as candidate_id, c.email as email, c.firstname as firstname, c.lastname as lastname, c.notes as notes, IFNULL(t.name, 'None') as task FROM `candidates` c LEFT JOIN (`progresslog` p INNER JOIN `tasks` t ON p.task_id = t.task_id) ON c.candidate_id = p.candidate_id WHERE p.timestamp = (SELECT MAX(timestamp) FROM progresslog p WHERE p.candidate_id = c.candidate_id) OR NOT EXISTS(SELECT candidate_id FROM `progresslog` WHERE candidate_id = p.candidate_id)");
		$query->setFetchMode(PDO::FETCH_CLASS, 'Candidate');
        $result = $query->fetchAll();
        return $result;
	}
	
	public function emptyTable(){
		$sql="DELETE FROM `".DB_TABLE."`";
		$statement = $this->databaseHandler->prepare($sql);
		$statement->execute();
	}

}

//$connection = new DatabaseController();
//print_r($connection->selectData());

?>