<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("requests.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Creates a new file in the database
 * PARAMETERS: api/files/create.php <apikey> <path> <user> <modification>
 */

if (isset($_GET['apikey']) and
    isset($_GET['path']) and
    isset($_GET['user']) and
    isset($_GET['modification']) and 
    isset($_GET['dateModified'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $path = (string) $_GET['path'];
        $user = (string) $_GET['user'];
        $modification = (string) $_GET['modification'];
        $dateModified = (string) $_GET['dateModified'];
        if(!fileExists($path, $user))
            createFile($path, $user, $modification, $dateModified);
        else{
            $actualModification = getFileModification($path, $user);
            if($actualModification != $modification){
                $who = getFileComputers($path, $user);
                requestFileDelete($actualModification, $who);
                resetFileChunks($path, $user);
                setFileModification($path, $user, $modification);
                setFileStatus($path, $user, "pending");
            }
        }
        echo json_encode(array("result" => "ok"));
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>