<?php
require_once "database_controller.php";
require_once "email_controller.php";
require_once "EncryptionHelper.php";

function getGUID(){
	if (function_exists('com_create_guid')){
		return com_create_guid();
	}else{
		mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45);// "-"
		$uuid = chr(123)// "{"
		.substr($charid, 0, 8).$hyphen
		.substr($charid, 8, 4).$hyphen
		.substr($charid,12, 4).$hyphen
		.substr($charid,16, 4).$hyphen
		.substr($charid,20,12)
		.chr(125);// "}"
		return $uuid;
	}
}

function insertData($dataArray){
	$connection = new DatabaseController();
	$guid = getGUID();
	array_unshift($dataArray, $guid);

	$connection->insertData($dataArray);

	$sendemail = new EmailController();
	$sendemail->send($dataArray);
}

function selectData(){
	$connection = new DatabaseController();
	$result = $connection->selectData();
	return $result;
}

function selectWinnersData(){
	$connection = new DatabaseController();
	$result = $connection->selectWinnersData();
	return $result;
}


?>