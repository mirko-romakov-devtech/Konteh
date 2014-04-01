
<html>
<head>
<title>KONTEH - error</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>


<style>
body{
overflow:hidden
}
#mainContainer{
padding:10px;
width:800;
min-height:300;
color:rgb(50,196,237);
background-color:rgb(245,245,245);
position:absolute;
top:50%;
left:50%;
margin-top:-150px;
margin-left:-400;
display:none;
-webkit-box-shadow: 6px -7px 13px 0px rgba(50, 196, 237, 0.6);
-moz-box-shadow:    6px -7px 13px 0px rgba(50, 196, 237, 0.6);
box-shadow:         6px -7px 13px 0px rgba(50, 196, 237, 0.6);
-webkit-border-radius: 15px;
-moz-border-radius: 15px;
border-radius: 15px;
font-size:14pt;
}
</style>

</head>
<body>


<div id="mainContainer">
<img src="images/logo.png" width="170" /><br /><br />
<div id="message">
Congratulation, you have completed challange successfully!
</div>
<br /><br />

<div class="col-md-12" id="formContainer">
<form class="form-horizontal">
<div class="form-group">
	<label for="emailInput" class="control-label col-md-3">Email:</label>
	<div class="col-md-9">
	<div class="input-group">
	<span class="input-group-addon">@</span>
	<input type="text" id="emailInput"  class="form-control" placeholder="npr. p.petrovic@gmail.com" />
	</div>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-md-3">Your faforite task:</label>
	<div class="col-md-9">
		<select id="favoriteInput" class="form-control">
			<option>getting credential</option>
			<option>creating server</option>
			<option>Finding username and password for VNC</option>
			<option>Locating your file</option>
			
			
		</select>
	</div>

</div>

	<div class="form-group">
	<label class="control-label col-md-3">Give us your feedback:</label>
	<div class="col-md-9">
	<textarea cols="40" rows="6" id="feedbackInput" class="form-control"></textarea>
	<input type="submit" id = "finishBtn" class="btn btn-success" value="Comlete Challenge" />
	</div>
	</div>
	</form>
</div>
</div>

<!-- Latest compiled and minified JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">

$('#finishBtn').on("click", function(e){
	e.preventDefault();
	var parameters = {};
	$(".form-control").each(function(){
		var id = $(this).attr("id");
		var paramName = id.substring(0, id.indexOf("Input"));
		parameters[paramName] = $(this).val();
	});
	
	console.log(parameters);
	/*$.post('controllers/api.php', {action:"sendEmail", params: parameters}, function(response){
		//var data = JSON.parse(response);
		
	});*/
	//response:
	$('#formContainer').remove();
	$('#mainContainer').css({'width':'400', 'margin-left':'-200'});
	$('#message').css('color','green').text("You have successfully comleted f****** everything!");
})

var platno = $("<canvas id='body'></canvas>");
platno.css({'position': 'absolute','top':0,'left':0,'z-index':'-1'});
$('body').append(platno);


var s = window.screen;
var width = body.width = s.width;
var height = body.height = s.height;
var letters = Array(256).join(1).split('');
var slova = "DEVTECH ".split('');
var brojac = 0;
var draw = function () {
  
  body.getContext('2d').fillStyle='rgba(0,0,0,.05)';
  body.getContext('2d').fillRect(0,0,width,height);
  body.getContext('2d').fillStyle='#0F0';
  letters.map(function(y_pos, index){
    text = slova[brojac];
    x_pos = index * 10;
    body.getContext('2d').fillText(text, x_pos, y_pos);
    letters[index] = (y_pos > 758 + Math.random() * 1e4) ? 0 : y_pos + 10;
  });
  brojac++;
  if(brojac>slova.length-1) brojac=0;
};
setInterval(draw, 43);

console.log('.gb_na:')
console.log($('.gb_na'));

setTimeout(function(){
	$('#mainContainer').fadeIn(100);
},100);
</script>
</body>
</html>