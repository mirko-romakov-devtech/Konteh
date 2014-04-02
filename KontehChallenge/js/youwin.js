
$('#finishBtn').on("click", function(e){
	e.preventDefault();
	var parameters = {};
	$(".form-control").each(function(){
		var id = $(this).attr("id");
		var paramName = id.substring(0, id.indexOf("Input"));
		parameters[paramName] = $(this).val();
	});
	
	console.log(parameters);
	var dataObj = {};
	dataObj.action="sendEmail";
	dataObj.params = parameters;
	var str_data = JSON.stringify(dataObj);
	console.log(str_data);
	
	$.post('controllers/api.php', {data: str_data}, function(response){
		//var data = JSON.parse(response);
		console.log("DATA response from api.php: ");
		//console.log(data);
		$('#formContainer').remove();
		$('#mainContainer').css({'width':'400', 'margin-left':'-200'});
		//$('#message').css('color','green').text("You have successfully comleted f****** everything!");
		//$('#message').css('color','green').text(data.message);
		$('#message').css('color','green').html(response);
		
	});
	//response:
	
})

var canvas = $("<canvas id='body'></canvas>");
canvas.css({'position': 'absolute','top':0,'left':0,'z-index':'-1'});
$('body').append(canvas);


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
	$('#mainContainer').fadeIn(00);
},30);