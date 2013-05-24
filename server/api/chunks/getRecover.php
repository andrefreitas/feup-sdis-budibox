<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("chunks.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Returns the body of a chunk for recovery
 * PARAMETERS: api/chunks/getRecover.php <apikey> <owner> <modification> <number>
 */

if (isset($_GET['apikey']) and
    isset($_GET['owner']) and 
    isset($_GET['modification']) and 
    isset($_GET['number'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $owner = (string) $_GET['owner'];
        $modification = (string) $_GET['modification'];
        $number = intval( $_GET['number']);
        $chunkBody = getChunkForRecover($owner, $modification, $number);
        echo json_encode(array("result" => "ok", "chunk" => $chunkBody));
    }
    
 
}else{
    echo json_encode(array("result" => "missingParams"));
}
?>