<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("chunks.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Deletes the chunk recover
 * PARAMETERS: api/chunks/deleteChunkRecover.php <apikey> <owner> <modification> <number>
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
        deleteChunkRecover($owner, $modification, $number);
        echo json_encode(array("result" => "ok"));
    }
    
 
}else{
    echo json_encode(array("result" => "missingParams"));
}
?>