<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("requests.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Gets the requests from a computer
 * PARAMETERS: api/requests/getComputerRequests.php <apikey> <computerId>
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
        $requests = getComputerRequests($computerId);
        echo json_encode(array("result" => "ok", "requests" => $requests));
    }

}else{
    echo json_encode(array("result" => "missingParams"));
}
?>