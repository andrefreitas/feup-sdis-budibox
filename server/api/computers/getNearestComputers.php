<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("computers.php"); 
require_once("users.php");

/**
 * DESCRIPTION: Get's the nearest computers from a given location
 * PARAMETERS: api/computers/getNearestComputers.php <lat> <lon> 
 */

if (isset($_GET['lat']) and isset($_GET["lon"])) {
    $lat = floatval($_GET['lat']);
    $lon = floatval($_GET['lon']);
    $computers = getNearestComputers($lat, $lon, $limit=30);
    echo json_encode(array("result" => "ok", "computers" => $computers));
}
?>