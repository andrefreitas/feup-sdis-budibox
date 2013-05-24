<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("requests.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Confirm's that a computer retrieved the chunk that requested
 * PARAMETERS: api/requests/confirmRecoverChunk.php <apikey> <computerId> <modification> <chunkNumber>
 */

if (isset($_GET['apikey']) and
    isset($_GET['computerId']) and 
    isset($_GET['modification']) and
    isset($_GET['chunkNumber'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $computerId = (string) $_GET['computerId'];
        $modification = (string) $_GET['modification'];
        $chunkNumber = intval($_GET['chunkNumber']);
        confirmRecoverChunk($computerId, $modification, $chunkNumber );
        echo json_encode(array("result" => "ok"));
    }

}else{
    echo json_encode(array("result" => "missingParams"));
}
?>