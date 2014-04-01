<?php
$directories = array('controllers', 'Helpers', 'models', get_include_path());
set_include_path(implode(PATH_SEPARATOR, $directories));

require_once 'ConfigParser.php';
require_once 'dbhandler.php';
require_once 'EncryptionHelper.php';

$encriptor = new EncryptionHelper(ConfigParser::DBHOST(), ConfigParser::DBDATABASE(), ConfigParser::DBUSERNAME(), ConfigParser::DBPASSWORD());



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
	var jsonRequest = {
			guid: guid,
			action: "getCredentials"
	};
	$.post(url, {data: JSON.stringify(jsonRequest)},function(data){
		var response = JSON.parse(data);
		if(response.success) {
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
	<div class="page-header">
		<img id="target" src="turn/code.png">
		<div class="row col-md-offset-3">
			<img src="images/logo.png">
		</div>
	</div>

	<div class="col-md-12" id="mainContainer">
		<div id="mainImage" class="thumbnail">
			<img src="images/welldone.jpg" class="img-responsive" width="400">
		</div>

		<div class="col-md-offset-3" id="guidDetails">
			<h3>Your guid:</h3>
			<h4>
				<?php echo $link_object->GUID ?>
			</h4>
		</div>

		<div class="col-md-offset-3" id="instructions">
			<h3>Instructions:</h3>
			<ol>
				<!--<li>Get credential <span class="glyphicon glyphicon-question-sign" id="hint_1" data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='http://cdn.memegenerator.net/instances/400x/36284195.jpg' />"></span></li> -->
				<li>Get credential <span class="glyphicon glyphicon-question-sign"
					id="hint_1"></span>
				</li>
				<li>Create Server<span
					class="glyphicon glyphicon-question-sign hint" id="hint_2"
					data-toggle="tooltip" data-placement="top" data-html="true"
					title="<img   src='images/hint_2.jpg' />"></span>
				</li>

				<li>Find username and password for opening VNC connection<span
					class="glyphicon glyphicon-question-sign hint" id="hint_2"
					data-toggle="tooltip" data-placement="top" data-html="true"
					title="<img   src='images/hint_2.jpg' />"></span>
				</li>

				<li>Open VNC connection<span
					class="glyphicon glyphicon-question-sign hint" id="hint_2"
					data-toggle="tooltip" data-placement="top" data-html="true"
					title="<img   src='images/hint_2.jpg' />"></span>
				</li>

				<li>Locate file on your server <span
					class="glyphicon glyphicon-question-sign hint" id="hint_3"
					data-toggle="tooltip" data-placement="top" data-html="true"
					title="<img   src='images/hint_3.jpg' />" ></span>
				</li>
				<li>Follow the link and complete the challange</li>
			</ol>
		</div>

		<div class="col-md-offset-3" id="instructions">
			<h3>Download:</h3>
			<div id="documentation">
				<a href="APIDoc.pdf"><span class="glyphicon glyphicon-download-alt"></span>
					documentation</a>
			</div>
		</div>

		<div class="col-md-6 col-md-offset-3" id="troubleDiv">
			<a href="#troubleShooting" data-toggle="collapse">Troubleshooting <span
				class="glyphicon glyphicon-chevron-down"></span>
			</a>
			<div id="troubleShooting" class="collapse well">
				<p>In order to successfully complete the challenge, you will need to
					access newly created server by VNC through java applet. Browsers by
					default don’t allow applets to run (they require explicit
					permission). If your browser doesn’t allow applet to run, you will
					need to lower java security levels.</p>

				<p>Enter Java control panel (C:\Program Files
					(x86)\Java\jre7\bin\javacpl.exe) In security tab lower security
					level to medium. Reopen the browser</p>
			</div>

		</div>

		<!--  
	<div class="col-md-6 col-md-offset-3 panel-group">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="panel-title">
					<a href="#troubleShooting" data-toggle="collapse" >Troubleshooting</a>
				</div>
			</div>
			<div  id="troubleShooting" class="panel-collapse collapse">
				<div class="panel-body">
				<p>In order to successfully complete the challenge, you will need to access newly created server by VNC through java applet. Browsers by default don’t allow applets to run (they require explicit permission).  If your browser doesn’t allow applet to run, you will need to lower java security levels.</p>
	
			<p>Enter Java control panel (C:\Program Files (x86)\Java\jre7\bin\javacpl.exe)
			In security tab lower security level to medium. 
			Reopen the browser</p>
			</div>
			</div>
			
		</div>
	</div>
-->
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
