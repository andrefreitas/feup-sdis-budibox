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
	$params = array("status" => $status);
	if($status == 'on'){
	    $params["lastTimeAlive"] = new MongoDate();
	}
	$newData = array('$set' => $params);
	$computers->update(array("user" => $user, "name" => $name), $newData);
}

function getComputerStatus($user, $name){
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
    $newData = array('$set' => array("location" => array("type" => "Point", "coordinates" => array($lon, $lat))));
    $computers->update(array("user" => $user, "name" => $name), $newData);
}

function getComputerLocationById($computerId){
    global $computers;
    $computer = $computers->findOne(array("_id" => new MongoId($computerId)), array("location" => true));
    if ($computer){
        $location = $computer["location"]["coordinates"];
        $lon = $location[0];
        $lat = $location[1];
        return array("lon" => $lon, "lat" => $lat);
    }
}
function getBestComputers($lat, $lon){
    global $computers;
    $geometry = array('$geometry' => array("type" => "Point", "coordinates" => array($lon, $lat)));
    $statement = array("location" => array('$near' => $geometry), "status" => "on");
    $cursor =  $computers->find($statement);
    $data = array();
    foreach($cursor as $doc){
        $data[] = (string) $doc["_id"];
    }
    return $data;
}

?>