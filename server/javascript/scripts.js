$(document).ready(function(){
	
});

function createUser(name, email, password){
	$.getJSON("api/createUser.php?",{
		name: name,
        email: email,
        password: password
	},
    function(data){
		console.log(data);
		alert(data);
	});
}