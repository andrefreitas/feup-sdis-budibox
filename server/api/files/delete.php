<?php
//header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("requests.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Updates the file status
 * PARAMETERS: api/files/setStatus.php <apikey> <path> <user> <status>
 */
if (isset($_GET['apikey']) and
    isset($_GET['path']) and
    isset($_GET['user'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $path = (string) $_GET['path'];
        $user = (string) $_GET['user'];
        $modification = getFileModification($path, $user);
        $status = getFileStatus($path, $user);
        if($status!="pending"){
            $who = getFileComputers($path, $user);
            requestFileDelete($modification, $who);
        }
        removeFile($path, $user);
        echo json_encode(array("result" => "ok"));
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>