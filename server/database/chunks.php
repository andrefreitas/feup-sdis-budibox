<?php
/* -----------------------
 * Chunks collection
 * -----------------------
 * JSON document schema:
 * {
 *     file_id : <ObjectId()>,
 *     modification: <sha256 modification>,
 *     number : <chunk number>
 *     body : <chunk body>,
 * }
 */
require_once("connection.php");
require_once("computers.php");
require_once("requests.php");

/* Set chunks collection */
$chunks = $db->chunks; 

function putChunk($fileId, $modification, $number, $body, $lat, $lon){
    global $chunks;
    
    // (1) Add chunk
    $chunks->insert(array("file_id" => new MongoId($fileId),
                          "modification" => $modification,
                          "number" => $number,
                          "body" => $body
                    ));
    
    // (2) Find the best computers from that location
    $computersIds = getBestComputers($lat, $lon);
    $who = array();
    foreach($computersIds as $computerId){
        $who[] = new MongoId($computerId);
    }
    requestStoreChunk($who, $fileId, $modification, $number);
}
?>