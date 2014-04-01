<?php
	error_reporting();
	require_once "/controllers/login_controller.php";
	require_once "/controllers/home_controller.php";

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