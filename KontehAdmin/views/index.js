function placeLogin(){
	var window_height = $(window).height();
    var window_width = $(window).width();
    var div_height = $('#login').height();
    var div_width = $('#login').width();
    position_y = (window_height/2) - (div_height/2);
    position_x = (window_width/2) - (div_width/2);
    $('#login').css('top', position_y).css('left', position_x);
}

function fillTable(){
	$.post("ajax/ajax.php", {action: "selectData"}, function(data){
				var ArrayOfObjects = JSON.parse(data);
				var output = "<tr><th>CANDIDATE ID</th><th>FIRST NAME</th><th>LAST NAME</th><th>E-MAIL ADDRESS</th></tr>";
				for(var i = 0; i < ArrayOfObjects.length; i++){
					output += "<tr><td>"+ArrayOfObjects[i].candidate_id+"<td>"+ArrayOfObjects[i].firstname+"</td><td>"+ArrayOfObjects[i].lastname+"</td><td>"+ArrayOfObjects[i].email+"</td></tr>";
				}

				$('#myTable').html(output);
			});
}

function getSession(){
	$.post("ajax/ajax.php", {action: "getSession", getSession: 1}, function(data){
		if(data == 1){
			$('body').load('views/main.html');
			fillTable();
		}
	});
}

$(document).ready(function(){

	placeLogin();
	getSession();
	
	$('#login_button').click(function(){

		var username = $("#username").val();
		var password = $("#password").val();

		$.post("ajax/ajax.php", {action: "login", username: username, password: password}, function(data){
			getSession();
		});

	});

});