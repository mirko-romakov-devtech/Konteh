<?php
	error_reporting();
	include "../controllers/login_controller.php";
	include "../controllers/home_controller.php";

	$username = $_POST['username'];
	$password = $_POST['password'];
	$action = $_POST['action'];

	$email = $_POST['email'];
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$notes = $_POST['notes'];


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
			insertData($email, $firstname, $lastname, $notes);
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