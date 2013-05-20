<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("chunks.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: This API put's a chunk in the temporary collection to be used to backup across the network
 * PARAMETERS: api/chunks/put.php <apikey> <fileId> <modification> <body> <number> <lat> <lon>
 * NOTES: The fileId is the "_id" from the files collection and the modification is the sha256.
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
        $number = intval($_GET['number']);
        $lat = floatval($_GET['lat']);
        $lon = floatval($_GET['lon']);
        
        putChunk($fileId, $modification, $number, $body, $lat, $lon);
        $computers = getBestComputers($lat, $lon);
        $computersIds = array();
        foreach($computers as $computer){
            $computers_ids[] = new MongoId($computer);
        }

        echo json_encode(array("result" => "ok"));
    }
    
 
}else{
    echo json_encode(array("result" => "missingParams"));
}
?>