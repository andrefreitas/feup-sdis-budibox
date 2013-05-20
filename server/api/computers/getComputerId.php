<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("computers.php"); 
require_once("users.php");

/**
 * DESCRIPTION: Get's the computer id by giving the user email and the computer name
 * PARAMETERS: api/computers/getComputerId.php <apikey> <user> <computer> 
 */

if (isset($_GET['apikey']) and isset($_GET["user"]) and isset($_GET["computer"])) {
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $user = (string) $_GET["user"];
        $computer = (string) $_GET["computer"];
        if(!userExists($user)){
            echo json_encode(array("result" => "invalidUser"));
        } else {
            $id = getComputerID($user, $computer);
            echo json_encode(array("result" => "ok", "computerId" => $id));
        }
    }
    
} else {
    echo json_encode(array("result" => "misingParams"));
}
?>