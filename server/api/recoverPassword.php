<?php
chdir("..");
chdir("database");
require_once("users.php");
chdir("../actions/");
require_once("email.php");

if(isset($_GET["email"]) and userExists((string) $_GET["email"])){
    recoverPassword($_GET["email"]);
    echo json_encode(array("result" => "emailSent"));
}else if(isset($_GET["email"]) and !userExists((string) $_GET["email"])){
    echo json_encode(array("result" => "invalidEmail"));
}else{
    echo json_encode(array("result" => "missingParams"));
}
?>