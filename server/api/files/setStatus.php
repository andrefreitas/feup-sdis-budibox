<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("users.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Updates the file status
 * PARAMETERS: api/files/setStatus.php <apikey> <path> <user> <status>
 */
if (isset($_GET['apikey']) and
    isset($_GET['path']) and
    isset($_GET['user']) and
    isset($_GET['status'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $path = (string) $_GET['path'];
        $user = (string) $_GET['user'];
        $status = (string) $_GET['status'];
        $oldStatus = getFileStatus($path, $user);
        setFileStatus($path, $user, $status);
        if($oldStatus =="active" and $status=="deleted"){
            $fileSize = getFileSize($path, $user);
            addUserSpaceUsage($user, -$fileSize);
        }
        if($oldStatus =="deleted" and $status=="active"){
            $fileSize = getFileSize($path, $user);
            addUserSpaceUsage($user, $fileSize);
        }
        echo json_encode(array("result" => "ok"));
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>