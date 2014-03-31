<?php

require_once("../models/candidate_model.php");
require_once("../include/config.php");

class DatabaseController{

	private $databaseHandler;

	public function __construct() {
		$this->databaseHandler = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASS);
	}

	public function insertData($params) {
		$sql = "INSERT INTO `".DB_TABLE."` (candidate_id, email, firstname, lastname, notes) VALUES (?, ?, ?, ?, ?)";
		$statement = $this->databaseHandler->prepare($sql);
		$statement->execute($params);
	}

	public function selectData(){
		$result = "";
		$query = $this->databaseHandler->query("select * from `".DB_TABLE."`");
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