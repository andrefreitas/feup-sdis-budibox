<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("computers.php");
require_once("users.php");

if (isset($_GET["user"]) and isset($_GET["computer"]) and isset($_GET["lon"]) and isset($_GET["lat"]) ) {
      $user = (string) $_GET["user"];
      $computer = (string) $_GET["computer"];
      $lon = (string) $_GET["lon"];
      $lat = (string) $_GET["lat"];
      if (computerExists($user, $computer)) {
          setComputerLocation($user, $computer , $lat, $lon);
          echo json_encode(array("result" => "ok"));
      } else {
          echo json_encode(array("result" => "error"));
      }
} else {
    echo json_encode(array("result" => "misingParams"));
}
?>