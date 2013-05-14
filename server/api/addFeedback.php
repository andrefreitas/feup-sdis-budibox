<?php
header('Content-type: application/json');
chdir("..");
chdir("database");
require_once("feedback.php");

if(isset($_GET["user"]) and isset($_GET["message"])){
    $user = (string) $_GET["user"];
    $message = (string) $_GET["message"];
    addFeedback($user, $message);
    echo json_encode(array("result" => "ok"));
}else{
    echo json_encode(array("result" => "missingParams"));
}

?>