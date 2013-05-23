<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("users.php");
chdir("..");
require_once("configuration.php");


/**
 * DESCRIPTION: Increments the offer usage
 * PARAMETERS: api/users/incOfferUsage.php <apikey> <user> <value>
 */
if(isset($_GET['apikey']) and isset($_GET['user']) and isset($_GET["value"])){
    $auth = (string) $_GET['apikey'];
    $user = (string) $_GET['user'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }else if (!userExists($user)) {
        echo json_encode(array("result" => "invalidUser"));
    }
    else{
        $value = intval($_GET["value"]);
        incrementUserOfferUsage($user, $value);
        echo json_encode(array("result" => "ok"));
    }
}
else{
    echo json_encode(array("result" => "missingParams"));
}
?>