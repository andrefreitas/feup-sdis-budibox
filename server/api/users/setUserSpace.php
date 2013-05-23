<?php 

header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("users.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Sets the user space
 * PARAMETERS: /api/users/setUserSpace.php <apikey> <user> <space> 
 */

if(isset($_GET['apikey']) and isset($_GET['user']) and isset($_GET['space'])) {
	$auth = (string) $_GET['apikey'];
	$user = (string) $_GET['user'];
	$space = intval ($_GET['space']);
	
	if ($auth != $apikey){
		echo json_encode(array("result" => "permissionDenied"));
	} else if (!userExists($user)) {
		echo json_encode(array("result" => "invalidUser"));
	}
	else{
		$valid = updateUserSpaceUsed($user, $space);
		if ($valid) {
			echo json_encode(array("result" => "ok"));
		}
		else {
			echo json_encode(array("result" => "notEnoughSpace"));
		}
	}
}
else {
	return json_encode(array("result" => "missingParams"));
}

?>