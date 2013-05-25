$(document).ready(function(){
	
	/* Load User info */
	loadUserInfo();
	$(".userInfo .avatar img").delay(3000).css("visibility", "visible");
	
	/* Links */
	$('.logouti').click(function(){
		window.location.href="actions/logout.php";
	});
	
	$('.boxi').click(function(){
		window.location.href="box.php";
	});
	
	$('.feedbacki').click(function(){
		$('#giveFeedbackModal').reveal();
	});
	
	

	/* Items li */
	$('.directory').click(function(){
		var basePath = $('.path').text().trim();
		var dir = basePath + $(this).text() + "/";
		window.location.href="box.php?dir=" + dir;
	});
	
	/* Mouse hover active file */
	$('.active .file').hover(function(){
		var actions = '<button type="button" class="download">Download</button> <button type="button" class="delete">Delete</button>';
		$(this).children(".actions").html(actions);
		
		/* Bind delete event */
		$('.active .file .actions button.delete').click(function(){
			var file = $(this).parent().parent();
			var path = getPath(file, 15),
				status = "deleted",
				user = getUserSession()["email"];
			if (confirm('Are you sure you want to delete '+ path + ' ?')) { 
					setFileStatus(user, path, status);
					document.location.reload()
				}
		
		});
	}		
	,function(){
		$(this).children(".actions").html("");
	});
	
	/* Mouse hover deleted file */
	$('.deleted .file').hover(function(){
		var actions = '<button type="button" class="restore">Restore</button> <button type="button" class="permanentDelete">Permanent Delete</button>';
		$(this).children(".actions").html(actions);
		
		/* Bind restore event */
		$('.deleted .file .actions button.restore').click(function(){
			var file = $(this).parent().parent();
			var path = getPath(file, 24),
				status = "active",
				user = getUserSession()["email"];
			if (confirm('Are you sure you want to restore '+ path + ' ?')) { 
					setFileStatus(user, path, status);
					document.location.reload()
				}
		
		});
		
		/* Bind permanent delete event */
		$('.deleted .file .actions button.permanentDelete').click(function(){
			var file = $(this).parent().parent();
			var path = getPath(file, 24),
				user = getUserSession()["email"];
			if (confirm('Are you sure you want to permanent delete '+ path + ' ?')) { 
					permanentDeleteFile(user, path)
					document.location.reload()
				}
		
		});
		
	}		
	,function(){
		$(this).children(".actions").html("");
	});
	
/*
 *  <button type="button" class="download">Download</button> <button type="button" class="delete">Delete</button>
 */
});

/*
 * Get path
 */
function getPath(item, removeLast){
	var basePath = $('.path').text().trim();
	var path = basePath + $(item).text();
	return path.substring(0, path.length - removeLast);
}

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

function setFileStatus(user, path, status){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/files/setStatus.php?",{
		user: user,
        path: path,
        status: status
	});
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"]);
}

function permanentDeleteFile(user, path){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/files/delete.php?",{
		user: user,
        path: path,
	});
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"]);
}