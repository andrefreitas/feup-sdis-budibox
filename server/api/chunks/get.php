<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("chunks.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Returns the body of a chunk
 * PARAMETERS: api/chunks/get.php <apikey> <fileId> <modification> <number>
 * NOTES: The fileId is the "_id" from the files collection and the modification is the sha256.
 */

if (isset($_GET['apikey']) and
    isset($_GET['fileId']) and 
    isset($_GET['modification']) and 
    isset($_GET['number']) 
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $fileId = (string) $_GET['fileId'];
        $modification = (string) $_GET['modification'];
        $number = intval( $_GET['number']);
        $chunkBody = getChunkBody($fileId, $modification, $number);
        echo json_encode(array("result" => "ok", "chunk" => $chunkBody));
    }
    
 
}else{
    echo json_encode(array("result" => "missingParams"));
}
?>