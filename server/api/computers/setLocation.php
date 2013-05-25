<?php
//header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("computers.php");
require_once("users.php");

/**
 * DESCRIPTION: Sets the computer geo location
 * PARAMETERS: api/computers/notifyStartup.php <apikey> <user> <computer> <lon> <lat>
 */
if (isset($_GET["apikey"]) and isset($_GET["user"]) and isset($_GET["computer"]) and isset($_GET["lon"]) and isset($_GET["lat"]) ) {
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $user = (string) $_GET["user"];
        $computer = (string) $_GET["computer"];
        $lon = floatval ($_GET["lon"]);
        $lat = floatval ($_GET["lat"]);
        if (computerExists($user, $computer)) {
            setComputerLocation($user ,  $computer , $lat, $lon);
            echo json_encode(array("result" => "ok"));
        } else {
            echo json_encode(array("result" => "error"));
        }
    }

} else {
    echo json_encode(array("result" => "misingParams"));
}
?>