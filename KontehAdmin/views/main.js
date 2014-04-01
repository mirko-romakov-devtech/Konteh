var window_height = $(window).height();
var window_width = $(window).width();
var div_height = $('#main').height();
var div_width = $('#main').width();
position_y = (window_height/2) - (div_height/2);
position_x = (window_width/2) - (div_width/2);
$('#main').css('top', position_y).css('left', position_x);

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
};

$('#logout_button').click(function(){
	$.post("ajax/ajax.php", {action: "logout"}, function(data){
		getSession();
		location.reload();
	});
});

$('#add_new').click(function(){

	email = $("#email").val();
	firstname = $("#firstname").val();
	lastname = $("#lastname").val();
	notes = $("#notes").val();

	if(email.trim() == "" || firstname.trim() == "" || lastname.trim() == ""){
		alert("You must enter e-mail address, first name and last name");
	}
	else{
		if(!isValidEmailAddress(email)){
			alert("Email address must be in proper format");
		}
		else{
			
			dataObject = {email: email, firstname: firstname, lastname: lastname, notes: notes};
			
			$.post("ajax/ajax.php", {action: "insertData", dataObject: dataObject}, function(data){
				alert("You have successfully added new candidate");
				$('#myModal').modal('hide');
				$("#guid").val("");
				$("#email").val("");
				$("#firstname").val("");
				$("#lastname").val("");
				$("#notes").val("");
				fillTable();
			});
		}
		
	}
	

});