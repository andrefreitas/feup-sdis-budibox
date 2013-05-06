<?php

header('Content-type: application/json');
chdir("..");
chdir("database");
require_once("users.php");
if(isset($_GET["confirmationKey"]) and isset($_GET["newPassword"])){
    $confirmationKey = (string) $_GET["confirmationKey"];
    $password = (string) $_GET["newPassword"];
    updateUserPasswordByConfirmationKey($confirmationKey, $password);
    echo json_encode(array("result" => "ok"));
}
else{
    echo json_encode(array("result" => "missingParams"));
}
?>