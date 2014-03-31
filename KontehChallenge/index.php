<?php

include("EncryptionHelper.php");
class ConfigParser {
	private static function getDatabaseConfig($asKey) {
		$config = parse_ini_file("config.ini", true);
		
		return $config['DB'][$asKey];
	}
	
	public static function DBHOST(){
		return self::getDatabaseConfig('host');
	}
	
	public static function DBUSERNAME(){
		return self::getDatabaseConfig('username');
	}
	
	public static function DBPASSWORD(){
		return self::getDatabaseConfig('password');
	}
	
	public static function DBDATABASE(){
		return self::getDatabaseConfig('database');
	}
}

class DBHandler
{
	private $_db;

	public function __construct(){
		try {
			$config = parse_ini_file("config.ini", true);
		    $this->_db = new PDO("mysql:host={$config['DB']['host']};dbname={$config['DB']['database']}", $config['DB']['username'], $config['DB']['password']);
		    $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e) {
		    //return Response::error("There is a problem with database. Please try again later");
			echo $e->getMessage();
		}
	}
}

$db = new DBHandler();

$encriptor = new EncryptionHelper(ConfigParser::DBHOST(), ConfigParser::DBDATABASE(), ConfigParser::DBUSERNAME(), ConfigParser::DBPASSWORD());
var_dump($encriptor);

if(isset($_GET["key"])){
	$key = $_GET["key"];
	$link_object = $encriptor->decryptObject($key);
	echo $key;
	var_dump($link_object);
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
<style>

body{
background-color:rgb(245,245,245);
}
#mainImage{
/*background-color:rgb(50,196,237);*/
background-color:rgb(245,245,245);
max-height:400px;
padding:0px
}

.glyphicon-question-sign{
	cursor:pointer
}

#documentation{
 cursor:pointer
}
#mainImage{
border:0
}

h3{
	font-weight:bold
}

.tooltip-inner {
  color: #000;
  background: black;
  border: solid 1px #000000;
  max-width:800px;
  max-height:400px;
  opacity:1
}
.tooltip.in {
  opacity: 1;
  filter: alpha(opacity=100);
}

#guidDetails h4{
color:red
}
</style>
<script type="text/javascript">
//Get credential from Database
function getCredential(guid){
	var url = "www.devtechgroup/challage/blabla";
	$.post(url,{action: "getCredential", guid: guid},function(data){
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
	<div  id="mainImage" class="col-md-12 thumbnail"><img src="images/welldone.jpg" class="img-responsive" width="400"></div>
	
	<div class="col-md-12 col-md-offset-3" id="guidDetails">
		<h3>Your guid:</h3>
		<h4><?php echo $link_object->GUID ?></h4>
	</div>
	
	<div class="col-md-12 col-md-offset-3" id="instructions">
		<h3>Instructions:</h3>
		<ol>
		<!--<li>Get credential <span class="glyphicon glyphicon-question-sign" id="hint_1" data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='http://cdn.memegenerator.net/instances/400x/36284195.jpg' />"></span></li> -->
		<li>Get credential <span class="glyphicon glyphicon-question-sign" id="hint_1"  ></span></li>
		<li>Create Server<span class="glyphicon glyphicon-question-sign hint" id="hint_2"  data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='images/hint_2.jpg' />"></span></li>
		
		<li>Find username and password for opening VNC connection<span class="glyphicon glyphicon-question-sign hint" id="hint_2"  data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='images/hint_2.jpg' />"></span></li>
		
		<li>Locate file on your server <span class="glyphicon glyphicon-question-sign hint" id="hint_3"  data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='images/hint_3.jpg'/>" ></span></li>
		<li>Follow the link and complete the challange</li>
		</ol>
	</div>

	<div class="col-md-12 col-md-offset-3" id="instructions">
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