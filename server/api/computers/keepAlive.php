<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("requests.php");
require_once("computers.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Keeps the computer on in the database
 * PARAMETERS: api/computers/keepAlive.php <apikey> <computerId>
 */

if (isset($_GET['apikey']) and
    isset($_GET['computerId'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $computerId = (string) $_GET['computerId'];
        keepComputerAlive($computerId);
        detectOffComputers();
        echo json_encode(array("result" => "ok"));
    }

}else{
    echo json_encode(array("result" => "missingParams"));
}
?>