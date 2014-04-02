<?php
$directories = array('controllers', 'Helpers', 'models', get_include_path());
set_include_path(implode(PATH_SEPARATOR, $directories));

require_once 'Helpers/ConfigParser.php';
require_once 'controllers/dbhandler.php';
require_once 'Helpers/EncryptionHelper.php'; 

$encriptor = new EncryptionHelper(ConfigParser::DBHOST(), ConfigParser::DBDATABASE(), ConfigParser::DBUSERNAME(), ConfigParser::DBPASSWORD());
//var_dump($encriptor);

if(isset($_GET["key"])){
	$key = $_GET["key"];
	$link_object = $encriptor->decryptObject($key);
	if($link_object->GUID == null) {
		//header("Location: error.php");
		
	}
	else {
		$usedKey = $model->isKeyUsed($link_object->GUID);
		//if($usedKey)
			//header("Location: error.php");
	}
	
	
}
else{
	//header("Location: error.php");
}

$guid = "{9FD4E880-D545-53D6-B507-19A21C7CF694}";
?>

<html>
<head>
<title>KONTEH - error</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/main.css">
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>


</head>
<body>


<div id="mainContainer">
<img src="images/logo.png" width="170" /><br /><br />
<div id="message">
Congratulation, you have completed challange successfully!
</div>
<br /><br />

<div class="col-md-12" id="formContainer">
<form class="form-vertical">
<div class="form-group">
	<label for="emailInput" class="control-label">Email:</label>
	
	<div class="input-group">
	<span class="input-group-addon">@</span>
	<input type="text" id="emailInput"  class="form-control" placeholder="your_email@gmail.com" />
	<input type="hidden" id="guidInput" class="form-control" value="<?php echo $guid ?>" />
	</div>

</div>

<div class="form-group">
	<label class="control-label">Your favorite task:</label>
	
		<select id="favoriteInput" class="form-control">
			<option>getting credential</option>
			<option>creating server</option>
			<option>Finding username and password for VNC</option>
			<option>Locating your file</option>
			
			
		</select>
	

</div>

	<div class="form-group">
	<label class="control-label">Give us your feedback:</label>
	
	<textarea cols="40" rows="6" id="feedbackInput" class="form-control"></textarea>
	</div>
	<input type="submit" id = "finishBtn" class="btn btn-success" value="Comlete Challenge" />
	
	</form>
</div>
</div>

<!-- Latest compiled and minified JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/youwin.js"></script>
</body>
</html>