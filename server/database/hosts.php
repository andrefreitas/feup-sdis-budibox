<?php
require_once("connection.php");
/* 
 * Hosts manipulation functions 
 */

function createHost($user, $hostname){
	global $db;
	$hosts = $db->hosts; 
	$hosts->insert(array( "user" => $user,
					      "hostname" => $hostname,
			              "setupDate" => new MongoDate(),
						  "status" => "on",
						  "lastTimeAlive" => new MongoDate()				 
	));
}

function setHostStatus($hostname,$status){
	global $db;
	$hosts = $db->hosts;
	$newData = array('$set' => array("status" => $status));
	$hosts->update(array("hostname" => $hostname), $newData);
}

function getHostStatus($hostname){
	global $db;
	$hosts = $db->hosts;
	$data = $hosts->findOne(array("hostname" => $hostname),array("status" => true));
	if($data)
		return $data["status"];
}


?>