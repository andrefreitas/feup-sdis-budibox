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

if (isset($_POST['apikey']) and
    isset($_POST['modification']) and 
    isset($_POST['number']) and 
    isset($_POST['body']) and 
    isset($_POST['owner'])
){
    $auth = (string) $_POST['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $modification = (string) $_POST['modification'];
        $number = intval($_POST['number']);
        $body = (string) $_POST['body'];
        $owner = (string) $_POST['owner'];
        removeGiveChunkRequest($modification, $number, $owner);
        requestRecoverChunk($owner, $modification, $number, $body);
        echo json_encode(array("result" => "ok"));
    }
    
 
}else{
    echo json_encode(array("result" => "missingParams"));
}
?>