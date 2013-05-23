<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("chunks.php");
require_once("files.php");
require_once("users.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: This API put's a chunk in the temporary collection to be used to backup across the network
 * PARAMETERS: api/chunks/put.php <apikey> <fileId> <modification> <body> <number> <lat> <lon>
 * NOTES: The fileId is the "_id" from the files collection and the modification is the sha256.
 */

if (isset($_POST['apikey']) and
    isset($_POST['fileId']) and 
    isset($_POST['modification']) and 
    isset($_POST['body']) and 
    isset($_POST['number']) and 
    isset($_POST['lat']) and
    isset($_POST['lon'])
){
    $auth = (string) $_POST['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $fileId = (string) $_POST['fileId'];
        $modification = (string) $_POST['modification'];
        $body = (string) $_POST['body'];
        $number = intval($_POST['number']);
        $lat = floatval($_POST['lat']);
        $lon = floatval($_POST['lon']);
        putChunk($fileId, $modification, $number, $body, $lat, $lon);
        
        // Update space usage
        $size = strlen($body);
        $user = getFileUser($fileId);
        addUserSpaceUsage($user, $size);
        incrementFileSize($fileId, $size);
        echo json_encode(array("result" => "ok"));
    }
    
 
}else{
    echo json_encode(array("result" => "missingParams"));
}
?>