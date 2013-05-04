<?php 
chdir("../database");
require_once("users.php");
chdir("../common");
require_once("init.php");

function sendConfirmationEmail($userEmail){
    global $smarty;
    $key = getUserConfirmationKey($userEmail);
    $name = getUser($userEmail);
    $name = $name["name"];
    $smarty->assign('user', $name);
    $smarty->assign('key', $key);
    
    // Prepare email fields
    $message = $smarty->fetch('welcome.tpl');
    $headers = "From: noreply@budibox.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    mail($userEmail,"Welcome to Budibox!", $message, $headers);
   
}

?>