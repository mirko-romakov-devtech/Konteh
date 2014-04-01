<?php
	error_reporting();
	include "../controllers/login_controller.php";
	include "../controllers/home_controller.php";

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
			echo "nothing";
			break;
	}


	//$data = "mirko";
	//insertData(11145, $data, $data, $data, $data);


?>