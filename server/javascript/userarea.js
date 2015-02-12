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

	$('.accounti').click(function(){
		$('#editAccountModal').reveal();
	});

	$('.peersi').click(function(){
		$('#peersLocationModal').reveal();

	});

	google.maps.event.addDomListener(window, 'load', showPeersMap);

	/* Items li */
	$('.directory').click(function(){
		var basePath = $('.path').text().trim();
		var dir = basePath + $(this).text() + "/";
		window.location.href="box.php?dir=" + dir;
	});

	/* Mouse hover active file */
	$('.active .file').hover(function(){
		var actions = '<button type="button" class="delete">Delete</button>';
		$(this).children(".actions").html(actions);

		/* Bind delete event */
		$('.active .file .actions button.delete').click(function(){
			var file = $(this).parent().parent();
			var path = getPath(file, 6),
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


	/* Give Feedback */
	$(".giveFeedback button").click(function(){
		var text = $(".giveFeedback textarea").val();
		user = getUserSession()["email"];
		if(text.length > 1)
			addFeedback(user, text);
	});

	/* Offer spinner */
	var spinner = $( "#spinner" ).spinner();

	/* Edit Account */
	$(".editAccount button").click(function(){
		var data = $(".editAccount").serializeArray();
		var	name = data[0]["value"],
			newEmail = data[1]["value"],
			password = data[2]["value"],
			offer = data[3]["value"],
			email = data[4]["value"];

		// Set params
		var params = {};
		params["name"] = name;
		params["email"] = email;
		params["newEmail"] = newEmail;
		params["offer"] = offer;

		if(password.length > 0){
			params["password"] = password;
		}
		var emailRegex= /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		if(!emailRegex.test(newEmail)){
			alert("Invalid Email");
		}
		else{
			updateUser(params);
			alert("Changes done sucessfully!");
		}
	});

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
        default: "https://andrefreitas.pt/budibox/images/default-avatar.png",
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

function updateUser(data){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/users/update.php?",data);
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

function addFeedback(user, text){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/addFeedback.php?",{
		user: user,
        message: text,
	});
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"]);
}


/* Peers location */

function showPeersMap(){

	/* My Location */
	var lon = 0;
	var lat = 0;
	var computers = getNearestComputers(lat, lon)["computers"];

	var name = computers[0]["name"];
	lon = computers[0]["lon"];
     lat = computers[0]["lat"];
	/* Create map */
	var mapOptions = {
			zoom: 10,
		    center: mapsLocation(lat,lon),
		    mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

	/* Positions */
	for (var i = 0; i < computers.length; i++) {
		var name = computers[i]["name"];
		var lon = computers[i]["lon"];
		var lat = computers[i]["lat"];
		putMapsMarker(map, name, lat, lon);
	}

}

function mapsLocation(lat, lon){
	return new google.maps.LatLng(lat, lon);
}

function putMapsMarker(map, text, lat, lon){
	return new google.maps.Marker({
	      position: mapsLocation(lat, lon),
	      map: map,
	      title: text
		});

}

function getNearestComputers(lat, lon){
	$.ajaxSetup( { "async": false } );
	var data = $.getJSON("api/computers/getNearestComputers.php?",{
		lat: lat,
        lon: lon,
	});
	$.ajaxSetup( { "async": true } );
	return $.parseJSON(data["responseText"]);
}
