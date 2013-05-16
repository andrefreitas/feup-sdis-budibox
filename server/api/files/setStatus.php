<?php
//header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
chdir("..");
require_once("configuration.php");


if (isset($_GET['apikey']) and
    isset($_GET['path']) and
    isset($_GET['user']) and
    isset($_GET['status'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $path = (string) $_GET['path'];
        $user = (string) $_GET['user'];
        $status = (string) $_GET['status'];
        setFileStatus($path, $user, $status);
        echo json_encode(array("result" => "ok"));
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>