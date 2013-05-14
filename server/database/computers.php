<?php
/* -----------------------
 * Computers collection
 * -----------------------
 * JSON document schema:
 * {
 *     user : <user email>,
 *     name : <computer name>,
 *     setupDate : <computer setup date>,
 *     status : <status of the computer>,
 *     lastTimeAlive : <last time the computer was online>  
 * }
 */
require_once("connection.php");

/* Set computers collection */
$computers = $db->computers; 

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
function updateComputersEmail($oldUserEmail, $newUserEmail){
    global $computers;
    $newData = array('$set' => array("user" => $newUserEmail));
    $computers->update(array("user" => $oldUserEmail), $newData, array("multiple" => true));
}

function getComputerID($user, $name){
    global $computers;
    $result = $computers->findOne(array("user" => $user, "name" => $name), array("_id" => true));
    if($result)
        return $result["_id"];
}

function computerExists($user, $name){
    global $computers;
    $result = $computers->findOne(array("user" => $user, "name" => $name), array("_id" => true));
    return ($result != null);
}

function setComputerLocation($user, $name, $lat, $lon){
    global $computers;
    $newData = array('$set' => array("location" => array("lat" => $lat, "lon" => $lon)));
    $computers->update(array("user" => $user, "name" => $name), $newData);
}
?>