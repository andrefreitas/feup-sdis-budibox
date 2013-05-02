<?php
require_once("connection.php");
/* 
 * Computers manipulation functions
 */
$computers = $db->computers; #collection

function createComputer($user, $name){
	global $computers;
	$computers->insert(array( "user" => $user,
    					      "name" => $name,
    			              "setupDate" => new MongoDate(),
    						  "status" => "on",
    						  "lastTimeAlive" => new MongoDate()				 
	));
}

function setComputerStatus($user, $name, $status){
	global $computers;
	$newData = array('$set' => array("status" => $status));
	$computers->update(array("user" => $user, "name" => $name), $newData);
}

function getHostStatus($user, $name){
	global $computers;
	$data = $computers->findOne(array("user" => $user, "name" => $name), array("status" => true));
	if($data)
		return $data["status"];
}


?>