<?php
    header('Content-type: application/json');
    chdir("..");
    chdir("database");
    require_once("users.php");

    if(isset($_GET["email"]) and isset($_GET["password"]) and userExists((string)$_GET["email"]) 
    and getUserStatus((string)$_GET["email"])=="active"){
        $email = (string) $_GET["email"];
        $password = (string) $_GET["password"];
        if (checkUserLogin( $email, $password))
        	echo json_encode(array("result" => "ok"));
        else {
            echo json_encode(array("result" => "invalidLogin"));
        }
        
    }
    else echo json_encode(array("result" => "missingParams"));
?>