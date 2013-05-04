$(document).ready(function(){
	/* Register event */
	$(".register form button[name='register']").click(function(){
		$(".notifications").empty();
		var data = $(".register form").serializeArray(),
			name = data[0]["value"],
			email = data[1]["value"],
			password1 = data[2]["value"],
			password2 = data[3]["value"];
        if(registrationIsValid(name, email, password1, password2)){
        	var data = createUser(name,email, password1);
        	if(data["result"] == "userAlreadyExists"){
        		$(".notifications").append("<div class='error'>The email <u>" + email + "</u> is already in use!</div>");
        		$(".error").fadeIn("slow");
        	}else{
        		$(".notifications").append("<div class='confirmation'>Registration done! A confirmation email has been sent to <u>" + email + "</u></div>");
            	$(".confirmation").fadeIn("slow");
        	}
        
        }else{
        	$(".error").fadeIn("slow");
        }
	});
	
	/* Confirmation notification */
	$(".confirmation").ready(function(){
		$(".confirmation").fadeIn("slow");
	});
	
	/* Error notification */
	$(".error").ready(function(){

	    $(".error").effect( "bounce", 
	            {times:3}, 300 );
	});
});

function registrationIsValid(name,email,password1,password2){
	var emailRegex= /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if(name.length < 1)
		$(".notifications").append("<div class='error'>Please write your name!</div>");
	else if(email.length == 0)
		$(".notifications").append("<div class='error'>Please write your email!</div>");
	else if(!emailRegex.test(email))
		$(".notifications").append("<div class='error'>Invalid email!</div>");
	else if(password1.length == 0)
		$(".notifications").append("<div class='error'>Please enter a password!</div>");
	else if(password1 != password2)
		$(".notifications").append("<div class='error'>Passwords didn't match!</div>");
	
	return ( password1.length > 0) & ( password2.length > 0) & ( password1 == password2 ) & ( name.length > 1 ) & emailRegex.test(email);
}

function createUser(name, email, password){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/createUser.php?",{
		name: name,
        email: email,
        password: password
	});
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"]);
}