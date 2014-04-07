<?php
$directories = array('../controllers', '../include', '../models', '../PHPMailer', get_include_path());
set_include_path(implode(PATH_SEPARATOR, $directories));

require_once "login_controller.php";
require_once "home_controller.php";
require_once 'email_controller.php';

////////////////////////////////////////////////////////
require_once 'database_controller.php';

echo "begin\n";
$db = new DatabaseController();
$enc = new EncryptionHelper(DB_HOST, DB_NAME, DB_USER, DB_PASS);

echo "lalala\n";


echo "before query\n";
$result = $db->executeQuery("SELECT * FROM candidates WHERE email NOT LIKE ?;", array("%@devtechgroup.com"));
echo "after query\n";
$i = 0;
foreach ($result as $item) {
	$email = new EmailController();
	$data = array(
			$item['candidate_id'],
			$item['email'],
			$item['firstname'],
			$item['lastname']
	);
	$email->send($data);
	echo "email sent to ".$item['email']."\n";
	$i++;
}
var_dump($i);

die();
////////////////////////////////////////////////////////