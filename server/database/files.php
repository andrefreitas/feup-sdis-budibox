<?php
require_once("connection.php");
/* Files Manipulation */

function createFile($path, $user){
    global $db;
    $files = $db->files;
    $files->insert(array("path" => $path, 
                         "user" => $user,
                         "status" => "pending",
                         "chunks" => array()
                   ));
}

function addFileChunk($path, $user, $hosts){
    global $db;
    $files = $db->files;
    $newData = array('$push' => array("chunks" => $hosts));
    $files->update(array("path" => $path, "user" => $user),$newData);
}

function setFileStatus($path, $user, $status){
    global $db;
    $files = $db->files;
    $newData = array('$set' => array("status" => $status));
    $files->update(array("path" => $path, "user" => $user),$newData);
}
?>