<?php
    chdir("../database");
    require_once("users.php");
    if(isset($_GET["key"])){
        $key = (string) $_GET["key"];
        activateUser($key);
        header('Location: ..');
    }
?>