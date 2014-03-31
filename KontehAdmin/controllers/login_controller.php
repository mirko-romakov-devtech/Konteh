<?php
error_reporting(0);
session_start();
include "../models/session_model.php";

function getSession(){
	$result = "";
	if(!isset($_SESSION[SessionModel::SESSION_CONSTANT]) || $_SESSION[SessionModel::SESSION_CONSTANT] == ""){
		$result = "0";
	}
	else{
		$result = "1";
	}

	return $result;
}

function login($user, $pass){
	if($user == "dunja" && md5($pass) == "2e5fa824ba946403f2d0eb4857eded49"){
		$_SESSION[SessionModel::SESSION_CONSTANT] = $pass;
	}
}

function logout(){
	session_destroy();
}

?>