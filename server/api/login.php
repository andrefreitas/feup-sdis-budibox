<?php
    header('Content-type: application/json');
    chdir("..");
    chdir("database");
    require_once("users.php");

    if( isset($_GET["auth"]) and $_GET["auth"]==$apikey
    and isset($_GET["email"]) and isset($_GET["password"]) and userExists((string)$_GET["email"])){
        if (checkUserLogin($_GET["email"],$_GET["password"]) > 0)
        	echo json_encode(array("result" => "ok"));
        
    }
    else echo json_encode(array("result" => "error"));
?>