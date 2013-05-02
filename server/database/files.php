<?php
/*-----------------------
*  Files Collection
* -----------------------
* JSON document schema:
* {
*     path : <file path>,
*     modification : <modification sha256>,
*     user : <user email>,
*     status : <status of the file>,
*     chunks : [
*               [ <computer id>, <computer id> ], // chunk 0
*               [ <computer id>, <computer id>, <computer id>], // chunk 1
*               ... // chunk n
*              ]
* }
*/
require_once("connection.php");
require_once("computers.php");

/* Set files collection */
$files = $db->files;

function createFile($path, $user, $modification){
    global $files;
    $files->insert(array("path" => $path, 
    					 "modification" => $modification,
                         "user" => $user,
                         "status" => "pending",
                         "chunks" => array()
                   ));
}


function addChunk($path, $user, $computers){
    global $files;
    foreach($computers as &$computer){
        $computer = new MongoId($computer);
    }
    $newData = array('$push' => array("chunks" => $computers));
    $files->update(array("path" => $path, "user" => $user),$newData);
}
/** Until here ---- **/
function addFileChunkHost($path,$user,$chunkNumber,$host){
    global $db;
    $files = $db->files;
    $newData = array('$addToSet' => array("chunks." . (string)$chunkNumber => $host));
    $files->update(array("path" => $path, "user" => $user),$newData);
}

function getChunkHosts($path,$user,$chunkNumber){
	global $db;
	$files = $db->files;
	$data = $files->findOne(array("path" => $path, "user" => $user));
	if(count($data) > 0 and count($data["chunks"])>$chunkNumber){
		return $data["chunks"][$chunkNumber];
	}
	
}

function deleteFileChunkHost($path,$user,$chunkNumber,$host){
	global $db;
	$files = $db->files;
	$newData = array('$pull' => array("chunks." . (string)$chunkNumber => $host));
	$files->update(array("path" => $path, "user" => $user),$newData);
}

function setFileStatus($path, $user, $status){
    global $db;
    $files = $db->files;
    $newData = array('$set' => array("status" => $status));
    $files->update(array("path" => $path, "user" => $user),$newData);
}

function setFileModification($path, $user, $modification){
	global $db;
	$files = $db->files;
	$newData = array('$set' => array("modification" => $modification));
	$files->update(array("path" => $path, "user" => $user),$newData);
}

function getFileModification($path, $user){
	global $db;
	$files = $db->files;
	$data = $files->findOne(array("path" => $path , "user" => $user),array("modification" => true));
	if( count($data) > 0 ){
		return $data["modification"];
	}
}

function resetFileChunks($path, $user){
	global $db;
	$files = $db->files;
	$newData = array('$set' => array("chunks" => array()));
	$files->update(array("path" => $path, "user" => $user),$newData);
}

function updateFilePath($user, $oldPath, $newPath){
	global $db;
	$files = $db->files;
	$newData = array('$set' => array("path" => $newPath));
	$files->update(array("path" => $oldPath, "user" => $user),$newData);
}
?>