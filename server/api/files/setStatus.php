<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("users.php");
chdir("..");
require_once("configuration.php");
chdir("common");
require_once("init.php");

/**
 * DESCRIPTION: Updates the file status
 * PARAMETERS: api/files/setStatus.php <apikey> <path> <user> <status>
 */
if (isset($_GET['path']) and
    isset($_GET['user']) and
    isset($_GET['status'])
){    
    
    // --> begin authentication
    $havePermission = false;
    if(isset($_GET['apikey'])){
        $auth = (string) $_GET['apikey'];
        $havePermission = $auth==$apikey;
    }else{
         $havePermission = ($_SESSION["email"] == $_GET['user']);
    }
    // --> end authentication
    
    if (!$havePermission){
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