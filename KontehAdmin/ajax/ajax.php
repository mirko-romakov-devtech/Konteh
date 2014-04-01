<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$directories = array('../controllers', '../include', '../models', '../PHPMailer', get_include_path());
set_include_path(implode(PATH_SEPARATOR, $directories));

require_once "login_controller.php";
require_once "home_controller.php";

$username = $_POST['username'];
$password = $_POST['password'];
$action = $_POST['action'];

$dataArray = $_POST['dataObject'];
$dataArray = array_values($dataArray);

switch ($action) {
	case 'getSession':
		echo getSession();
		break;

	case 'login':
		login($username, $password);
		break;

	case 'logout':
		logout();
		break;

	case 'insertData':
		insertData($dataArray);
		break;

	case 'selectData':
		print_r(json_encode(selectData()));
		break;

	default:
		echo "Invalid action provided.";
		break;
}

?>