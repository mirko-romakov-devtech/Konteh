<?php
require_once 'Helpers/ConfigParser.php';
require_once 'controllers/dbhandler.php';
require_once 'Helpers/EncryptionHelper.php';

$encriptor = new EncryptionHelper(ConfigParser::DBHOST(), ConfigParser::DBDATABASE(), ConfigParser::DBUSERNAME(), ConfigParser::DBPASSWORD());
//var_dump($encriptor);

if(isset($_GET["key"])){
	$key = $_GET["key"];
	$link_object = $encriptor->decryptObject($key);
	if($link_object->GUID == null) {
		header("Location: error.php");
	}

}
else{
	header("Location: error.php");
}


/*
function decodeKey($key){
	if ($key == "12345")
		$guid = "12345";
	else
		$guid = false;
		
	return $guid;
}*/
/*
if ($guid == false){
	header("Location: error.php");
}*/


?>
<html>
<head>
<title>KONTEH</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="turn/jquery-ui.min.js"></script>
<script type="text/javascript" src="turn/turn.js"></script>
<link rel="stylesheet" type="text/css" href="turn/turn.css">
<link rel="stylesheet" type="text/css" href="css/main.css">

<script type="text/javascript">
//Get credential from Database
function getCredentials(guid){
	var url = "controllers/api.php";
	$.post(url,{action: "getCredentials", guid: guid},function(data){
		var response = JSON.parse(data);
		if(data.success) {
			console.log("Your credentials: ");
			console.log(response);
		}
		else
			console.log("Something went wrong!");
	});
}

</script>
</head>
<body>
<div class="page-header"><img id="target" src="turn/code.png"><div class="row col-md-offset-3"><img src="images/logo.png"></div></div>

<div class="col-md-12" id="mainContainer">
	<div  id="mainImage" class="thumbnail"><img src="images/welldone.jpg" class="img-responsive" width="400"></div>
	
	<div class="col-md-offset-3" id="guidDetails">
		<h3>Your guid:</h3>
		<h4><?php echo $link_object->GUID?></h4>
	</div>
	
	<div class="col-md-offset-3" id="instructions">
		<h3>Instructions:</h3>
		<ol>
		<!--<li>Get credential <span class="glyphicon glyphicon-question-sign" id="hint_1" data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='http://cdn.memegenerator.net/instances/400x/36284195.jpg' />"></span></li> -->
		<li>Get credential <span class="glyphicon glyphicon-question-sign" id="hint_1"  ></span></li>
		<li>Create Server<span class="glyphicon glyphicon-question-sign hint" id="hint_2"  data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='images/hint_2.jpg' />"></span></li>
		
		<li>Find username and password for opening VNC connection<span class="glyphicon glyphicon-question-sign hint" id="hint_2"  data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='images/hint_2.jpg' />"></span></li>
		
		<li>Open VNC connection<span class="glyphicon glyphicon-question-sign hint" id="hint_2"  data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='images/hint_2.jpg' />"></span></li>
		
		<li>Locate file on your server <span class="glyphicon glyphicon-question-sign hint" id="hint_3"  data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='images/hint_3.jpg'/>" ></span></li>
		<li>Follow the link and complete the challange</li>
		</ol>
	</div>

	<div class="col-md-offset-3" id="instructions">
		<h3>Download:</h3>
		<div id="documentation"><a href="#" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> documentation</a></div>
	</div>

</div>



<!-- Latest compiled and minified JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
//load tooltips
$('.glyphicon-question-sign').tooltip();
$('div .tooltip-inner').css('min-width','1000');
//initialize first hint (page corner)
$(document).ready(function(){
	$( '#target' ).fold();
});
//show first hint 
$('#hint_1').hover(function(){
	$('#turn_object').stop().animate({width: 200, height: 200});
}, function(){
	$('#turn_object').stop().animate({width: 0, height: 0});
});

</script>
</body>
</html>