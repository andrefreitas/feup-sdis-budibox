<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("chunks.php");
require_once("requests.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Give a chunk to a computer restore
 * PARAMETERS: api/chunks/giveForRestore.php <apikey> <modification> <number> <body> <owner>
 */

if (isset($_GET['apikey']) and
    isset($_GET['modification']) and 
    isset($_GET['number']) and 
    isset($_GET['body']) and 
    isset($_GET['owner'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $modification = (string) $_GET['modification'];
        $number = intval($_GET['number']);
        $body = (string) $_GET['body'];
        $owner = (string) $_GET['owner'];
        removeGiveChunkRequest($modification, $number, $owner);
        requestRecoverChunk($owner, $modification, $number, $body);
        echo json_encode(array("result" => "ok"));
    }
    
 
}else{
    echo json_encode(array("result" => "missingParams"));
}
?>