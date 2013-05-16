<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("computers.php"); 
require_once("users.php");

if (isset($_GET["user"]) and isset($_GET["computer"])) {
    $user = (string) $_GET["user"];
    $computer = (string) $_GET["computer"];
    if(!userExists($user)){
        echo json_encode(array("result" => "invalidUser"));
    } else {
        if (!computerExists($user, $computer))
            createComputer($user, $computer);
        setComputerStatus($user, $computer, "on");
        echo json_encode(array("result" => "ok"));
    }
} else {
    echo json_encode(array("result" => "misingParams"));
}
?>