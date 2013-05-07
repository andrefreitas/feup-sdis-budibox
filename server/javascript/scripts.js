$(document).ready(function(){
	
	 if ($.browser.msie) {
	     $("html").html('<img src="images/noie.jpg">');
	   }
	 
	/* Register event */
	$(".register form button[name='register']").click(function(){
		$(".notifications").empty();
		$(".notifications").html('<div class="processing"><div class="loading"> <img src="images/loading.gif"></div> <div class="message">Processing your registration...</div></div>');
		var data = $(".register form").serializeArray(),
			name = data[0]["value"],
			email = data[1]["value"],
			password1 = data[2]["value"],
			password2 = data[3]["value"];
        if(registrationIsValid(name, email, password1, password2)){
        	var data = createUser(name,email, password1);
        	if(data["result"] == "userAlreadyExists"){
        		$(".notifications").html("<div class='error'>The email <u>" + email + "</u> is already in use!</div>");
        		 $(".error").effect( "bounce", 
        		            {times:3}, 300 );
        	}else if(data["result"] == "invalidEmailDomain"){
        		$(".notifications").html("<div class='error'>The email <u>" + email + "</u> doesn\'t exists!</div>");
       		    $(".error").effect( "bounce", 
       		            {times:3}, 300 );
        	}
        	else{
        		$(".notifications").html("<div class='confirmation'>Registration done! A confirmation email has been sent to <u>" + email + "</u></div>");
            	$(".confirmation").fadeIn("slow");
        	}
        
        }else{
        	 $(".error").effect( "bounce", 
     	            {times:3}, 300 );
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
	
	/* Forget Password */
	$(".forgot").click(function(){
		$("#header").html('<div class="limbo"></div><div class="recover"><form><input type="email" name="email" placeholder="Email" /> <button type="button">RECOVER PASSWORD</button></form></div>');
		
		/* Recover Password */
		$(".recover button").click(function(){
			var data = $(".recover form").serializeArray(),
			    email = data[0]["value"],
			    result = recoverPassword(email);
			if(result["result"]=="emailSent"){
				$("#header").html('<div class="container"><div class="confirmation">An email has been sent to ' + email +'</div></div>');
				$(".confirmation").fadeIn("slow");
			}
			else{
				$("#header").html('<div class="container"><div class=\'error\'>The email <u> '+ email + '</u> doesn\'t exists!</div></div>');
       		    $(".error").effect( "bounce", {times:3}, 300 );
			}

		});
	
	});
	
	/* Set new password */
	$(".setNewPassword button").click(function(){
		var data = $(".setNewPassword").serializeArray(),
		key = data[0]["value"],
	    password1 = data[1]["value"],
	    password2 = data[2]["value"];
		console.log(password1.length);
		if(password1.length == 0){
			$(".notifications").html("Please write a password!");
		}
		else if(password1.length>0 && password1==password2){
			var result = changePassword(key,password1);
			if(result["result"] == "ok"){
				$(".setNewPassword").html("<div align='center'><h1>Password changed successfully!</h2></div>");
			}
		}else{
			$(".notifications").html("Invalid Passwords!");
		}
	});
	
	/* Load User info */
	$('.userInfo .info').html('Andre Freitas<br/><span class="email">p.ddandrefreitas@gmail.com</span>');

});

function registrationIsValid(name,email,password1,password2){
	var emailRegex= /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if(name.length < 1)
		$(".notifications").html("<div class='error'>Please write your name!</div>");
	else if(email.length == 0)
		$(".notifications").html("<div class='error'>Please write your email!</div>");
	else if(!emailRegex.test(email))
		$(".notifications").html("<div class='error'>Invalid email!</div>");
	else if(password1.length == 0)
		$(".notifications").html("<div class='error'>Please enter a password!</div>");
	else if(password1 != password2)
		$(".notifications").html("<div class='error'>Passwords didn't match!</div>");
	
	return ( password1.length > 0) & ( password2.length > 0) & ( password1 == password2 ) & ( name.length > 1 ) & emailRegex.test(email);
}

function createUser(name, email, password){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/createUser.php?",{
		name: name,
        email: email,
        password: password,
        timeout: 3000
	});
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"]);
}

function recoverPassword(email){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/recoverPassword.php?",{
        email: email,
        timeout: 3000
	});
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"]);
}

function changePassword(confirmationKey, newPassword){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/changePassword.php?",{
		confirmationKey: confirmationKey,
		newPassword: newPassword,
        timeout: 3000
	});
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"]);
}

function loginIsValid(email, password){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/login.php?",{
		email: email,
		password: password,
        timeout: 3000
	});
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"])["result"] == "ok";
}

function validateLogin(){
	var data = $(".login form").serializeArray(),
		email = data[0]["value"],
		password = data[1]["value"];
	if(email.length == 0 || password.length == 0 || !loginIsValid(email, password)){
		$('.error').remove();
		$('.subnotifications').html("<div class='error'>Invalid Login!</div>");
		$(".error").effect( "bounce", 
	            {times:3}, 300 );
	}else{
		return true;
	}
	
	return false;
}