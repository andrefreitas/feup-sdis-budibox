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
*                  [ <computer id>, <computer id> ], // chunk 0
*                  [ <computer id>, <computer id>, <computer id>], // chunk 1
*                  ... // chunk n
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

function addComputerToChunk($path,$user,$chunkNumber,$computer){
    global $files;
    $newData = array('$addToSet' => array("chunks." . (string)$chunkNumber => new MongoId($computer)));
    $files->update(array("path" => $path, "user" => $user),$newData);
}

function getChunkComputers($path,$user,$chunkNumber){
	global $files;
	$data = $files->findOne(array("path" => $path, "user" => $user));
	if(count($data) > 0 and count($data["chunks"])>$chunkNumber){
		return $data["chunks"][$chunkNumber];
	}
	
}

function removeComputerFromChunk($path,$user,$chunkNumber,$computer){
	global $files;
	$newData = array('$pull' => array("chunks." . (string)$chunkNumber => new MongoId($computer)));
	$files->update(array("path" => $path, "user" => $user),$newData);
}

function setFileStatus($path, $user, $status){
    global $files;
    $newData = array('$set' => array("status" => $status));
    $files->update(array("path" => $path, "user" => $user),$newData);
}

function getFileStatus($path, $user){
    global $files;
    $result = $files->findOne(array("path" => $path, "user" => $user),array("status" => true));
    if($result)
        return $result["status"];
}

function setFileModification($path, $user, $modification){
	global $files;
	$newData = array('$set' => array("modification" => $modification));
	$files->update(array("path" => $path, "user" => $user),$newData);
}

function getFileModification($path, $user){
	global $files;
	$data = $files->findOne(array("path" => $path , "user" => $user),array("modification" => true));
	if( count($data) > 0 ){
		return $data["modification"];
	}
}

function resetFileChunks($path, $user){
	global $files;
	$newData = array('$set' => array("chunks" => array()));
	$files->update(array("path" => $path, "user" => $user),$newData);
}

function updateFilePath($user, $oldPath, $newPath){
	global $files;
	$newData = array('$set' => array("path" => $newPath));
	$files->update(array("path" => $oldPath, "user" => $user),$newData);
}

function updateFilesUser($oldUserEmail, $newUserEmail){
    global $files;
    $newData = array('$set' => array("user" => $newUserEmail));
    $files->update(array("user" => $oldUserEmail),$newData, array("multiple" => true));
}

function removeFilesFromUser($userEmail){
    global $files;
    $files->remove(array("user" => $userEmail));
}

function getFilesFromUser($userEmail){
    global $files;
    $cursor =  $files->find(array("user" => $userEmail), array("path" => true, "status" => true));
    $data = array();
    foreach($cursor as $doc){
        $data[] = $doc;
    }
    return $data; 
}

function fileExists($path, $user){
    global $files;
    $file = $files->findOne(array("user" => $user, "path" => $path), array("_id" => true));
    return $file;
}
/* 
 * Return the files from a given directory. Note: allways use / in the beginning 
 * and end of the directory name.
 */
function getDirectoryFiles($userEmail, $directory = "/", $status = "any"){
    global $db;
    $files = $db->files;
    $regex = array('$regex' => "^" . $directory . "[a-zA-Z0-9\.\-_\ ]+$");
    $statement = array( "user" => $userEmail, "path" => $regex);
    if ($status!="any") {
        $statement["status"] = $status;
    }
    $cursor =  $files->find($statement);
    $data = array();
    foreach($cursor as $doc){
        $path = $doc["path"];
        $path = substr($path, strlen($directory));
        $data[] = $path;
    }
    return $data;
}

function getDirectoryFolders($userEmail, $directory = "/"){
    global $db;
    $files = $db->files;
    $regex = array('$regex' => "^" . $directory . "[a-zA-Z0-9\-_\ ]+/");
    $statement = array( "user" => $userEmail, "path" => $regex);
    $cursor =  $files->find($statement);
    $data = array();
    foreach($cursor as $doc){
        $path = $doc["path"];
        $path = substr($path, strlen($directory));
        $path = explode('/', $path);
        $data[] = $path[0];
    }
    $data = array_unique($data);
    return $data;
}
?>