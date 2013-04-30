<?php
    header('Content-type: application/json');
    chdir("..");
    chdir("database");
    require_once("users.php");

    if( isset($_GET["auth"]) and $_GET["auth"]==$apikey and isset($_GET["name"])
    and isset($_GET["email"]) and isset($_GET["password"]) and !userExists((string)$_GET["email"])){
        $name = (string) $_GET["name"];
        $email = (string) $_GET["email"];
        $password = (string) $_GET["password"];
        createUser($_GET["name"],$_GET["email"],$_GET["password"]);
        echo json_encode(array("result" => "ok"));
        
    }
    else echo json_encode(array("result" => "error"));
?>