<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Gets the file id from the database
 * PARAMETERS: api/files/getId.php <apikey> <path> <user>
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
        if(fileExists($path, $user)){
            $id = getFileId($user, $path);
            echo json_encode(array("result" => "ok", "id" => $id));
        }
        else{
            echo json_encode(array("result" => "invalidFile"));
        }
       
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>