<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("requests.php");
require_once("users.php");
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
        if(!fileExists($path, $user)){
            echo json_encode(array("result" => "invalidFile"));
        }else{
            /* Delete the old chunks */
            $actualModification = getFileModification($path, $user);
    
            if($modification != $actualModification){
                $fileSize = getFileSize($path, $user);
                addUserSpaceUsage($user, -$fileSize);
                setFileSize($path, $user, 0);
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
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>