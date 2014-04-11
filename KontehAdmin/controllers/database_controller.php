<?php
require_once "candidate_model.php";
require_once "config.php";
require_once "candidate_log.php";

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
	
	public function getWinnerData($id = false, $sortTask = false){
		
		if($id == true) {
			$queryPart = " where c.candidate_id = ? ";
		} else {
			$queryPart = " where c.candidate_id in (select candidate_id from progresslog where task_id = 7) ";
		}
		
		$result = "";
		$sql = "select c.candidate_id, c.firstname, c.lastname, c.email, FROM_UNIXTIME(p.timestamp) as timestamp, p.task_id, t.name
				from candidates c left join progresslog p on c.candidate_id = p.candidate_id left join tasks t on p.task_id = t.task_id
				".$queryPart."
				group by c.candidate_id, p.task_id
				order by c.candidate_id,p.timestamp asc;";
		
		$query = $this->databaseHandler->prepare($sql);
		
		if($id == true) $query->execute(array($id));
		else $query->execute();
		
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		
		return $this->generateWinnerData($result, $sortTask);
	}
	
	public function generateWinnerData($logs, $sortTask) {
		$candidate_id = null;
		$tempTimestamp = null;
		$newResult = array();
		$initialTaskDate = null;
		$taskLogCount = 0;
		$initialDate = 0;
		$logCount = 0;
		foreach ($logs as $res){
			if($candidate_id==null){
				$candidate_id = $res['candidate_id'];
			}

			if($candidate_id!=$res['candidate_id']) {
				$tempTimestamp = null;
				$initialTaskDate = null;
				$taskLogCount = 0;
				$initialDate = 0;
				$logCount = 0;
				$candidate_id = $res['candidate_id'];
			}
			if(!$res['timestamp'])
				continue;
			if($tempTimestamp==null){
				$tempTimestamp = date_create($res['timestamp']);
				$initialDate = $tempTimestamp;
				$initialTaskDate = $tempTimestamp;
			}
				
			$time = date_create($res['timestamp']);
				
			$rowAdded = false;
			foreach($newResult as $newRes){
				if($newRes->candidate_id==$res['candidate_id']){
					$task = $newRes->tasks[count($newRes->tasks)-1];
					if(isset($task) && $task->task_id == $res['task_id']) {
						$task->timestamps[] = new TaskTimestampLog(date_diff($time,$tempTimestamp));
						$rowAdded = true;
						$taskLogCount++;
						if(abs($sortTask)==$res['task_id'])
							$newResult[count($newResult)-1]->sortVal = date_diff($time,$initialTaskDate)->format("%d%H%I%S");
					}
					else{
						$newRes->tasks[count($newRes->tasks)-1]->logLength = date_diff($tempTimestamp,$initialTaskDate)->format("%d Day(s) %H:%I:%S");
						$newRes->tasks[count($newRes->tasks)-1]->logCount = $taskLogCount;
						$newRes->tasks[] = new TaskLog($res['task_id'], $res['name'], date_diff($time,$tempTimestamp), date_diff($time,$initialTaskDate));
						if(abs($sortTask)==$res['task_id'])
							$newResult[count($newResult)-1]->sortVal = date_diff($time,$initialTaskDate)->format("%d%H%I%S");
						$rowAdded = true;
						$initialTaskDate=$time;
						$taskLogCount = 1;
					}
					
					$logCount++;
				}
			}
			if(!$rowAdded){
				$newResult[] = new CandidateLog($res['candidate_id'], $res['email'], $res['firstname'], $res['lastname'], new TaskLog($res['task_id'], $res['name'], date_diff($time,$tempTimestamp),date_diff($time,$initialTaskDate)));
				if(abs($sortTask)==$res['task_id'])
					$newResult[count($newResult)-1]->sortVal = date_diff($time,$initialTaskDate)->format("%d%H%I%S");
				$taskLogCount++;
				$logCount++;
			}
			$tempTimestamp = $time;
		}
		$newRes->tasks[count($newRes->tasks)-1]->logLength = date_diff($tempTimestamp,$initialTaskDate)->format("%d Day(s) %H:%I:%S");;
		$newRes->tasks[count($newRes->tasks)-1]->logCount = $taskLogCount;
		$newResult[0]->logLength = date_diff($initialDate,$tempTimestamp)->format("%d Day(s) %H:%I:%S");;
		$newResult[0]->logCount = $logCount;
		if(!$sortTask)
			return $newResult;
		$orderedCandidates = array();
		$minID = -1;
		$minVal = -1;
		$asdf = 0;
		while(count($orderedCandidates)< count($newResult)) {
			for ($i=0;$i<count($newResult);$i++){
				$candidateSorted = false;
				foreach($orderedCandidates as $is) {
					if($is->candidate_id == $newResult[$i]->candidate_id){
						$candidateSorted = true;
						continue;
					}
					
				}
				if($candidateSorted) continue;
				if($minID == -1) {
					$minID = $i;
					$minVal = $newResult[$i]->sortVal;
				}
				
				else if($newResult[$i]->sortVal<$minVal) {
					$minID = $i;
					$minVal = $newResult[$i]->sortVal;
				}
			}
			$orderedCandidates[] = $newResult[$minID];
			$minID = -1;
			$minVal = -1;
		}
		
		return $orderedCandidates;
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
//print_r($connection->getWinnerData("{0912FBEC-F8C9-7EEB-4463-31EE55C2809E}"));

?>