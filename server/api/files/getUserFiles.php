<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Gets the files from the user
 * PARAMETERS: api/files/getUserFiles.php <apikey> <path> <user>
 */
if (isset($_GET['apikey']) and
    isset($_GET['user'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $userEmail = (string) $_GET['user'];
        $files = getFilesFromUser($userEmail);
        echo json_encode(array("result" => "ok", "files" => $files));
       
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>