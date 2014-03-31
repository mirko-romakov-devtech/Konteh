<?php
include "../controllers/database_controller.php";

	$connection = new DatabaseController();
	$connection->emptyTable();

?>