<?php

class CandidateLog{

	public $candidate_id;
	public $email;
	public $firstname;
	public $lastname;
	public $logLength;
	public $logCount;
	public $sortVal;
	public $tasks = array();
	
	public function __construct($id, $email, $firstname, $lastname, $task){
		$this->candidate_id = $id;
		$this->email = $email;
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->tasks[] = $task;
	}
	
}

class TaskLog {
	public $task_id;
	public $name;
	public $timeToCompleteTask;
	public $logLength;
	public $logCount;
	public $timestamps = array();
	
	public function __construct($id, $name, $timestamp, $timeToCompleteTask){
		$this->task_id = $id;
		$this->name = $name;
		$this->timeToCompleteTask = $timeToCompleteTask->format("%d Day(s) %H:%I:%S");
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