<?php
    header('Content-type: application/json');
    chdir("..");
    chdir("database");
    require_once("users.php");
    chdir("../actions/");
    require_once("email.php");

    if(!isset($_GET["name"]) or  !isset($_GET["email"]) or !isset($_GET["password"])){
        echo json_encode(array("result" => "missingParams"));
    }
    else if(!userExists((string)$_GET["email"]) ){
        $name = (string) $_GET["name"];
        $email = (string) $_GET["email"];
        $password = (string) $_GET["password"];
        $emailDomain = explode("@", $email);
        $emailDomain = $emailDomain[1];
        if(gethostbyname($emailDomain) != $emailDomain){
            createUser($_GET["name"],$_GET["email"],$_GET["password"]);
            sendConfirmationEmail($email);
            echo json_encode(array("result" => "ok"));
        }else{
            echo json_encode(array("result" => "invalidEmailDomain"));
        }
        
    }
    else echo json_encode(array("result" => "userAlreadyExists"));
?>