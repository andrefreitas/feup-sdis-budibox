<?php
chdir("../common");
require_once("init.php");
chdir("../database");
require_once("users.php");
if(isset($_GET["email"]) and isset($_GET["password"])){
    $email = (string) $_GET["email"];
    $password = (string) $_GET["password"];
    if(checkUserLogin($email, $password)){
        $_SESSION = getUser($email);
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
?>