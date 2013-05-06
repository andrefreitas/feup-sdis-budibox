<?php
    chdir("../database");
    require_once("users.php");
    if(isset($_GET["key"])){
        $key = (string) $_GET["key"];
        if(!confirmationKeyIsValid($key)){
            header('Location: ../index.php?welcome=0');
        }
        else if(!userIsActive($key)){
            activateUser($key);
            header('Location: ../index.php?welcome=1');
        }
       else{
            header('Location: ../index.php?welcome=2');
        }
    }
?>