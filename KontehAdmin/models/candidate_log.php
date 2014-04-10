<?php

class CandidateLog{

	public $candidate_id;
	public $email;
	public $logLength;
	public $logCount;
	public $tasks = array();
	
	public function __construct($id, $email, $task){
		$this->candidate_id = $id;
		$this->email = $email;
		$this->tasks[] = $task;
	}
	
}

class TaskLog {
	public $task_id;
	public $name;
	public $logLength;
	public $logCount;
	public $timestamps = array();
	
	public function __construct($id, $name, $timestamp){
		$this->task_id = $id;
		$this->name = $name;
		$this->timestamps[] = new TaskTimestampLog($timestamp);
	}
}

class TaskTimestampLog {
	public $timestamp;
	
	public function __construct($timestamp) {
		$this->timestamp = $timestamp;
	}
}

?>