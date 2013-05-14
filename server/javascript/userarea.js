$(document).ready(function(){
	/* Load User info */
	loadUserInfo();
	$(".userInfo .avatar img").delay(3000).css("visibility", "visible");
	
	/* Links */
	$('.logouti').click(function(){
		window.location.href="actions/logout.php";
	});
	
	
	/* Links */
	$('.boxi').click(function(){
		window.location.href="box.php";
	});
	
	/* Items li */
	$('.directory').click(function(){
		var basePath = $('.path').text().trim();
		var dir = basePath + $(this).text() + "/";
		window.location.href="box.php?dir=" + dir;
	});
	
	$('.file').hover(function(){
		var actions = '<button type="button" class="download">Download</button> <button type="button" class="delete">Delete</button>';
		$(this).children(".actions").html(actions);
	}		
	,function(){
		$(this).children(".actions").html("");
	});
	
/*
 *  <button type="button" class="download">Download</button> <button type="button" class="delete">Delete</button>
 */
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