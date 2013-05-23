<?php
/*-----------------------
*  Files Collection
* -----------------------
* JSON document schema:
* {
*     path : <file path>,
*     modification : <modification sha256>,
*     user : <user email>,
*     status : <status of the file: {pending, active, deleted}>,
*     size : <file size in bytes>,
*     chunks : [
*                  [ <computer id>, <computer id> ], // chunk 0
*                  [ <computer id>, <computer id>, <computer id>], // chunk 1
*                  ... // chunk n
*              ],
*     date_modified : <modification date>
*          
* }
*/
require_once("connection.php");
require_once("computers.php");

/* Set files collection */
$files = $db->files;

function createFile($path, $user, $modification, $dateModified){
    global $files;
    $dateModified = new MongoDate(strtotime($dateModified));
    $files->insert(array("path" => $path, 
    					 "modification" => $modification,
                         "user" => $user,
                         "status" => "pending",
                         "chunks" => array(),
                         "size" => 0,
                         "date_modified" => $dateModified
                   ));
}

function setFileModificationDate($path, $user, $dateModified){
    global $files;
    $dateModified = new MongoDate(strtotime($dateModified));
    $files->update(array("path" => $path, "user" => $user),
                   array('$set' => array("date_modified" => $dateModified)));                              

}

function getFileModificationDate($path, $user){
    global $files;
    $dateModified = new MongoDate(strtotime($dateModified));
    $file = $files->findOne(array("path" => $path, "user" => $user), array("date_modified" => true));
    if($file){
        $date = date("c", $file["date_modified"]->sec);
        return $date;
    }
    return false;
}

function incrementFileSize($fileId, $size){
    global $files;
    $newData = array('$inc' => array("size" => $size));
    $fileId = new MongoId($fileId);
    $files->update(array("_id" => $fileId), $newData);
}

function getFileSize($path, $user){
    global $files;
    $file = $files->findOne(array("path" => $path, "user" => $user), array("size" => true));
    if($file){
        return $file["size"];
    }
    return -1;
}

function setFileSize($path, $user, $size){
    global $files;
    $newData = array('$set' => array("size" => $size));
    $files->update(array("path" => $path, "user" => $user), $newData);
}


function addChunk($path, $user, $computers){
    global $files;
    foreach($computers as &$computer){
        $computer = new MongoId($computer);
    }
    $newData = array('$push' => array("chunks" => $computers));
    $files->update(array("path" => $path, "user" => $user),$newData);
}

function addComputerToChunk($fileId, $chunkNumber, $computer){
    global $files;
    $file = $files->findOne(array("_id" => new MongoId($fileId)));
    $chunkNumber = intval($chunkNumber);
    if($file["chunks"][$chunkNumber] == NULL){
        $newData = array('$set' => array("chunks." . (string)$chunkNumber => array(new MongoId($computer))));
        $files->update(array("_id" => new MongoId($fileId)), $newData);
    } else{
        $newData = array('$addToSet' => array("chunks." . (string)$chunkNumber => new MongoId($computer)));
        $files->update(array("_id" => new MongoId($fileId)), $newData);
    }
}

function removeComputerFromChunk($modification, $chunkNumber, $computerId){
    global $files;
    $file = $files->findOne(array("modification" => $modification));
    $chunkNumber = intval($chunkNumber);
    $computerId = new MongoId($computerId);
    if($file["chunks"][$chunkNumber] != NULL){
        $newData = array('$pull' => array("chunks." . (string)$chunkNumber => $computerId));
        $files->update(array("modification" => $modification), $newData);
    } 
}

function getChunkComputers($path,$user,$chunkNumber){
	global $files;
	$data = $files->findOne(array("path" => $path, "user" => $user));
	if(count($data) > 0 and count($data["chunks"])>$chunkNumber){
		return $data["chunks"][$chunkNumber];
	}
	
}


function setFileStatus($path, $user, $status){
    global $files;
    $newData = array('$set' => array("status" => $status));
    $files->update(array("path" => $path, "user" => $user),$newData);
}

function setFileStatusById($fileId, $status){
    global $files;
    $fileId = new MongoId($fileId);
    $newData = array('$set' => array("status" => $status));
    $files->update(array("_id" => $fileId),$newData);
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
    $cursor =  $files->find(array("user" => $userEmail, "status" =>"active"), array("path" => true, "status" => true));
    $data = array();
    foreach($cursor as $doc){
        $data[] = $doc;
    }
    return $data; 
}

function fileExists($path, $user){
    global $files;
    $file = $files->findOne(array("user" => $user, "path" => $path), array("_id" => true));
    return isset($file["_id"]);
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
function getFileById($id){
    global $files;
    return $files->findOne(array("_id" => new MongoId($id)));
}


function getFileId($user, $path){
    global $files;
    $file =  $files->findOne(array("user" => $user, "path" => $path));
    if($file)
        return (string)$file["_id"];
    return false;
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


function getFileComputers($path, $user){
    global $files;
    $result = $files->findOne(array("path" => $path, "user" => $user));
    $chunks = $result["chunks"];
    if(count($chunks) > 0){
        $computersIds = array();
        foreach($chunks as $computers){
             $computersIds = array_merge($computersIds, $computers);
        }
        $computersIds = array_unique($computersIds);
        return $computersIds;
    }
    return array();
}


function removeFile($path, $user){
    global $files;
    $files->remove(array("path" => $path, "user" => $user));
}

function getFileUser($fileId){
    global $files;
    $fileId = new MongoId($fileId);
    $file = $files->findOne(array("_id" => $fileId), array("user" => true));
    if($file){
        return $file["user"];
    }
    return false;
}
?>