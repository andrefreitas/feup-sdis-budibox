<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("requests.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Removes a computer storage from a chunk number
 * PARAMETERS: api/chunks/deleted.php <apikey> <modification> <number> <computerId>
 */

if (isset($_GET['apikey']) and
    isset($_GET['modification']) and
    isset($_GET['number']) and
    isset($_GET['computerId'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $number = intval($_GET['number']);
        $modification = (string) $_GET['modification'];
        $computerId =  (string) $_GET['computerId'];
        removeComputerFromChunk($modification, $number, $computerId);
        echo json_encode(array("result" => "ok"));
    }

}else{
    echo json_encode(array("result" => "missingParams"));
}
?>