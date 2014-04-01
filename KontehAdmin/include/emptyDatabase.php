<?php
require_once "/controllers/database_controller.php";

	$connection = new DatabaseController();
	$connection->emptyTable();

?>