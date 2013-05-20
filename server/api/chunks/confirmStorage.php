<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("requests.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Confirm's a chunk storage from a computer
 * PARAMETERS: api/chunks/confirmStorage.php <apikey> <fileId> <number> <computerId> <modification>
 * NOTES: The fileId is the "_id" from the files collection.
 */

if (isset($_GET['apikey']) and
    isset($_GET['fileId']) and
    isset($_GET['number']) and
    isset($_GET['computerId']) and 
    isset($_GET['modification'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $fileId = (string) $_GET['fileId'];
        $number = intval($_GET['number']);
        $computerId = (string) $_GET['computerId'];
        $modification = (string) $_GET['modification'];
        addComputerToChunk($fileId, $number, $computerId);
        storeChunkDone($computerId, $fileId, $modification, $number);
        echo json_encode(array("result" => "ok"));
    }

}else{
    echo json_encode(array("result" => "missingParams"));
}
?>