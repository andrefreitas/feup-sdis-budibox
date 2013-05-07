$(document).ready(function(){
	/* Load User info */
	loadUserInfo();
	$(".userInfo .avatar img").delay(3000).css("visibility", "visible");

});

/*
* Get gravatar
*/
function getGravatar(email, size){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/getGravatar.php?",{
		email: email,
        default: "http://master.budibox.com/images/default-avatar.png",
        size: size
	});
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"])["url"];
}

function getUserSession(){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/getSession.php?");
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"]);
}

function loadUserInfo(){
	var info = getUserSession(),
		avatarURL = getGravatar(info["email"], 70);
	$('.userInfo .info').html(info["name"] + '<br/><span class="email">'+ info["email"]+ '</span>');
	$('.avatar img').attr('src',avatarURL);
	
}