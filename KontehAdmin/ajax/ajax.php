<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$directories = array('../controllers', '../include', '../models', '../PHPMailer', get_include_path());
set_include_path(implode(PATH_SEPARATOR, $directories));

require_once "login_controller.php";
require_once "home_controller.php";
require_once 'email_controller.php';

$action = $_POST['action'];

switch ($action) {
	case 'getSession':
		echo getSession();
		break;

	case 'login':
		$username = $_POST['username'];
		$password = $_POST['password'];
		login($username, $password);
		break;

	case 'logout':
		logout();
		break;

	case 'insertData':
		$dataArray = $_POST['dataObject'];
		$dataArray = array_values($dataArray);
		insertData($dataArray);
		break;

	case 'selectData':
		print_r(json_encode(selectData()));
		break;
		
	case 'sendEmail':
		sendEmail($_POST['emailData']);
		break;
	default:
		echo "Invalid action provided.";
		break;
}

function sendEmail($emailData) {
	$data = json_decode($emailData, true);
	$email = new EmailController();
	$email->sendSuccessMail($data);
}

?>