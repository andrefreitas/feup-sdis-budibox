<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("chunks.php");
chdir("..");
require_once("configuration.php");
/**
 * Example usage: api/chunks/put.php?apikey=12&fileId=1234&modification=looolll000l&body=olabomdia&number=0&lat=0&lon=0
 */

if (isset($_GET['apikey']) and
    isset($_GET['fileId']) and 
    isset($_GET['modification']) and 
    isset($_GET['body']) and 
    isset($_GET['number']) and 
    isset($_GET['lat']) and
    isset($_GET['lon'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $fileId = (string) $_GET['fileId'];
        $modification = (string) $_GET['modification'];
        $body = (string) $_GET['body'];
        $number = (string) $_GET['number'];
        $lat = floatval($_GET['lat']);
        $lon = floatval($_GET['lon']);
        addChunk( $fileId, $modification, $number, $body, $lat, $lon);
        echo json_encode(array("result" => "ok"));
    }
    
 
}else{
    echo json_encode(array("result" => "missingParams"));
}
?>