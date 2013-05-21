<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Updates the modification value from a file
 * PARAMETERS: api/files/modify.php <apikey> <path> <user> <modification>
 */
if (isset($_GET['apikey']) and
    isset($_GET['path']) and
    isset($_GET['user']) and
    isset($_GET['modification'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }   
    else {
        $path = (string) $_GET['path'];
        $user = (string) $_GET['user'];
        $modification = (string) $_GET['modification'];
        
        /* Delete the old chunks */
        $actualModification = getFileModification($path, $user);
        if($modification != $actualModification){
            $status = getFileStatus($path, $user);
            if($status != "pending"){
                $who = getFileComputers($path, $user);
                requestFileDelete($actualModification, $who);
            }
        }
        
        setFileModification($path, $user, $modification);
        resetFileChunks($path, $user);
        setFileStatus($path, $user, "pending");
        echo json_encode(array("result" => "ok"));
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>