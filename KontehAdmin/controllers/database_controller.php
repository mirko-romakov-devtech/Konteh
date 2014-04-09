<?php
require_once "candidate_model.php";
require_once "config.php";

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
		$query = $this->databaseHandler->query("SELECT c.candidate_id, c.firstname, c.email, c.lastname, IFNULL(MAX(p.task_id), \"none\") max_task_id, IFNULL((SELECT t.name FROM tasks t WHERE t.task_id = MAX(p.task_id)), \"none\") as task_name, IFNULL(MAX(p.timestamp), \"none\") progress_time 
				FROM progresslog p
				RIGHT JOIN candidates c ON c.candidate_id = p.candidate_id
				LEFT JOIN tasks t ON t.task_id = p.task_id
				GROUP BY c.candidate_id");
		$query->setFetchMode(PDO::FETCH_CLASS, 'Candidate');
        $result = $query->fetchAll();
        return $result;
	}
	
	public function selectWinnerData(){
		$result = "";
		$query = $this->databaseHandler->query("SELECT c.candidate_id, c.firstname, c.email, c.lastname
				FROM progresslog p
				RIGHT JOIN candidates c ON c.candidate_id = p.candidate_id
				WHERE c.candidate_id in (select candidate_id from progresslog where task_id = 7)
				GROUP BY c.candidate_id");
		$query->setFetchMode(PDO::FETCH_CLASS, 'Candidate');
		$result = $query->fetchAll();
		return $result;
	}
	
	public function emptyTable(){
		$sql="DELETE FROM `".DB_TABLE."`";
		$statement = $this->databaseHandler->prepare($sql);
		$statement->execute();
	}
	
	public function executeQuery($query, $params) {
		$statement = $this->databaseHandler->prepare($query);
		$statement->execute($params);
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

}

//$connection = new DatabaseController();
//print_r($connection->selectData());

?>