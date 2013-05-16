<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("users.php");
chdir("..");
require_once("configuration.php");
if(isset($_GET['apikey']) and isset($_GET['user'])){
    $auth = (string) $_GET['apikey'];
    $user = (string) $_GET['user'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }else if (!userExists($user)) {
        echo json_encode(array("result" => "invalidUser"));
    }
    else{
        $key = getUserKey($user);
        echo json_encode(array("result" => "ok", "key" => $key));
    }
}
else{
    echo json_encode(array("result" => "missingParams"));
}
?>