
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
#message{
padding:10px;
width:500;
height:300;
color:rgb(50,196,237);
background-color:rgb(245,245,245);
position:absolute;
top:50%;
left:50%;
margin-top:-150px;
margin-left:-250;
display:none;
-webkit-box-shadow: 6px -7px 13px 0px rgba(50, 196, 237, 0.6);
-moz-box-shadow:    6px -7px 13px 0px rgba(50, 196, 237, 0.6);
box-shadow:         6px -7px 13px 0px rgba(50, 196, 237, 0.6);
-webkit-border-radius: 15px;
-moz-border-radius: 15px;
border-radius: 15px;
font-size:14pt
}
</style>

</head>
<body>
<div id="message">
<img src="images/logo.png" width="170" /><br /><br />
Congratulation Pero Peric, you have completed challange successfully!
<br /><br />
Please contact us and give your feedback on following email address:<br />
challenge@devtechgroup.com
</div>

<!-- Latest compiled and minified JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">


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
	$('#message').fadeIn(5000);
},2000);
</script>
</body>
</html>