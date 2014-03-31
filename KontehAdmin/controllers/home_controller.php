<?php
	
	include "database_controller.php";
	include "email_controller.php";
	include "../include/EncryptionHelper.php";
	
	

	function insertData($email, $firstname, $lastname, $notes){
		$connection = new DatabaseController();
		$guid = getGUID();
		$connection->insertData(array($guid, $email, $firstname, $lastname, $notes));
		
		$sendemail = new EmailController($firstname, $lastname, $email, $guid);

	}

	function selectData(){
		$connection = new DatabaseController();
		$result = $connection->selectData();
		return $result;
	}


?>