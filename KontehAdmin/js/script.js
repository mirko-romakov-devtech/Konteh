function placeLogin(){
	var window_height = $(window).height();
    var window_width = $(window).width();
    var div_height = $('#login').height();
    var div_width = $('#login').width();
    position_y = (window_height/2) - (div_height/2);
    position_x = (window_width/2) - (div_width/2);
    $('#login').css('top', position_y).css('left', position_x);
}
function placeMain(){
	var window_height = $(window).height();
    var window_width = $(window).width();
    var div_height = $('#main').height();
    var div_width = $('#main').width();
    position_y = (window_height/2) - (div_height/2);
    position_x = (window_width/2) - (div_width/2);
    $('#main').css('top', position_y).css('left', position_x);
}

$(document).ready(function(){

	placeLogin();
	placeMain();

    $('#add_new').click(function(){
        name = $('#name').val();
        email = $('#email').val();
        url = $('#url').val();
        token = $('#token').val();
        if(name=='' || email=='' || url=='' || token==''){
            alert("You must fill in all fields");
        }else{
            $.post("ajax/ajax.php", {name: name, email: email, url: url, token: token}, function(data){
                $('#myModal').modal('hide');
                var array = data.split("^");
                alert(array[0]);
                $('#myTable').html(array[1]);
                $('#name').val("");
                $('#email').val("");
                $('#url').val("");
                $('#token').val("");
            });     
        }
    });

});