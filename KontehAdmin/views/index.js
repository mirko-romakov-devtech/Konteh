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
				var output = "<tr><th>CANDIDATE ID</th><th>FIRST NAME</th><th>LAST NAME</th><th>E-MAIL ADDRESS</th><th>STATUS</th></tr>";
				for(var i = 0; i < ArrayOfObjects.length; i++){
					output += "<tr><td>"+ArrayOfObjects[i].candidate_id+"<td>"+ArrayOfObjects[i].firstname+"</td><td>"+ArrayOfObjects[i].lastname+"</td><td>"+ArrayOfObjects[i].email+"</td><td>"+ArrayOfObjects[i].task_name+"</td></tr>";
				}

				$('#myTable').html(output);
			});
}

function fillTableWithWinners() {
	$.post("ajax/ajax.php", {action: "selectWinnersData"}, function(data){
		var ArrayOfObjects = JSON.parse(data);
		var output = "<tr><th>CANDIDATE ID</th><th>FIRST NAME</th><th>LAST NAME</th><th>E-MAIL ADDRESS</th></tr>";
		for(var i = 0; i < ArrayOfObjects.length; i++){
			candidate_id = ArrayOfObjects[i].candidate_id;
			output += '<tr><td><a href="#" data-toggle="modal" data-target="#winnerData" onclick="showWinner(\''+candidate_id+'\');">'+candidate_id+'</a></td><td><a href="#" data-toggle="modal" data-target="#winnerData" onclick="showWinner(\''+candidate_id+'\');">'+ArrayOfObjects[i].firstname+'</a></td><td><a href="#" data-toggle="modal" data-target="#winnerData" onclick="showWinner(\''+candidate_id+'\');">'+ArrayOfObjects[i].lastname+'</a></td><td><a href="#" data-toggle="modal" data-target="#winnerData" onclick="showWinner(\''+candidate_id+'\');">'+ArrayOfObjects[i].email+'</a></td></tr>';
		}

		$('#myTable').html(output);
	});
}

function showWinner(candidate_id){
	$.post("ajax/ajax.php", {action: "getWinnerData", id: candidate_id}, function(data){
		var ArrayOfObjects = JSON.parse(data);
		output = "<p class='head'>Candidate Name: <b>"+ArrayOfObjects[0].firstname+" "+ArrayOfObjects[0].lastname+"</b></p>";
		output += "<p class='head'>Candidate Email: <b>"+ArrayOfObjects[0].email+"</b></p>";
		output += "<p>Total time logged: "+ArrayOfObjects[0].logLength+"</p>";
		output += "<table class='table table-bordered table-striped'>";
		output += "<tr><th>TASK NAME</th><th class='text-center'>COMPLETION TIME</th></tr>";
		var taskNames = new Array();
		for(var i = 0; i < ArrayOfObjects[0].tasks.length; i++){
			if (taskNames.indexOf(ArrayOfObjects[0].tasks[i].name) < 0){	
				taskNames.push(ArrayOfObjects[0].tasks[i].name);
				output += "<tr><td>"+ArrayOfObjects[0].tasks[i].name+"</td><td class='text-center'>"+ArrayOfObjects[0].tasks[i].timeToCompleteTask+" ("+ArrayOfObjects[i].tasks[j].rank+")</td></tr>";
			}
		}
		
		output += "</table>";
		//output += "<br/> Average time per task: 3:00 min ";
			$('#winner_results').html(output);
		});
}

$(document).on("click", ".sortable", function() {
	var element = $(this);
	var taskID = element.data('id');
	$.post("ajax/ajax.php", {action: "sortResults", task: taskID}, function(data){
		$(".sortable").removeClass("sorted");
		output = returnData(data);
		$('#winners_table tbody').html(output);
		element.addClass("sorted");
	});
});

function returnData(data){
	var ArrayOfObjects = JSON.parse(data);
	var output = "";
	for(var i = 0; i < ArrayOfObjects.length; i++){
		var taskNames = new Array();
			output += "<tr><td>"+ArrayOfObjects[i].firstname+" "+ArrayOfObjects[i].lastname+"</td>";
			var tasks = new Array("1","3","4","5","6","7");
			for(var k = 0; k<tasks.length;k++)
			{
				var hasTime = false;
				for(var j = 0; j < ArrayOfObjects[i].tasks.length; j++){
						if(ArrayOfObjects[i].tasks[j].task_id == tasks[k]) {
							output += "<td style='white-space: nowrap;'>"+ArrayOfObjects[i].tasks[j].timeToCompleteTask+" ("+ArrayOfObjects[i].tasks[j].rank+")</td>";
							hasTime = true;
							continue;
						}
				}
				if(!hasTime)
					output+="<td></td>";
			}
			output += "</tr>";
	}
	
	return output;
}

function showResults(){
	$.post("ajax/ajax.php", {action: "getResults"}, function(data){
		output = returnData(data);
		$('#winners_table tbody').html(output);
	});
}

function getSession(){
	$.post("ajax/ajax.php", {action: "getSession"}, function(data){
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