<?php
require_once "candidate_model.php";
require_once "config.php";
require_once "candidate_log.php";

class DatabaseController{

	private $databaseHandler;

	public function __construct() {
		$this->databaseHandler = new PDO('mysql:dbname='.DB_NAME.';host=localhost'/*.DB_HOST*/, DB_USER, DB_PASS);
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
	
	public function selectWinnersData(){
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
	
	public function getWinnerData($id){
		$result = "";
		$sql = "select c.candidate_id, c.email, FROM_UNIXTIME(p.timestamp) as timestamp, p.task_id, t.name
				from candidates c left join progresslog p on c.candidate_id = p.candidate_id left join tasks t on p.task_id = t.task_id
				where c.candidate_id = ?
				order by c.candidate_id,p.timestamp asc;";
		$query = $this->databaseHandler->prepare($sql);
		
		$query->execute(array($id));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		
		$tempTimestamp = null;
		$newResult = array();
		$currentTask = 0;
		foreach ($result as $res){
			if($tempTimestamp==null){
				$tempTimestamp = date_create($res['timestamp']);
				$res['timestamp'] = 'Started at: '.$res['timestamp'];
			}
			else{
				$time = date_create($res['timestamp']);
				$res['timestamp'] =  date_diff($time,$tempTimestamp);
				$tempTimestamp = $time;
			}
			$rowAdded = false;
			foreach($newResult as $newRes){
				if($newRes->candidate_id==$res['candidate_id']){
					$task = $newRes->tasks[count($newRes->tasks)-1];
					if(isset($task) && $task->task_id == $res['task_id']) {
						
						$task->timestamps[] = new TaskTimestampLog($res['timestamp']);
						$rowAdded = true;
					}
					else{
						$newRes->tasks[] = new TaskLog($res['task_id'], $res['name'], $res['timestamp']);
						$rowAdded = true;
					}
				}
			}
			if(!$rowAdded){
				$newResult[] = new CandidateLog($res['candidate_id'], $res['email'], new TaskLog($res['task_id'], $res['name'], $res['timestamp']));
			}
		}
		return $newResult;
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