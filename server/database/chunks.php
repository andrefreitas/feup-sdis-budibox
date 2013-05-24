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


function getChunkBody($fileId, $modification, $number){
    global $chunks;
    $fileId = new MongoId($fileId);
    $chunk = $chunks->findOne(array("file_id" => $fileId, "modification" => $modification, "number" => $number), array("body" => true));
    if($chunk) return $chunk["body"];
}

function deleteChunk($fileId, $modification, $number){
    global $chunks;
    $fileId = new MongoId($fileId);
    $chunk = $chunks->remove(array("file_id" => $fileId, "modification" => $modification, "number" => $number));
}

function storeChunkForRecover($owner, $modification, $number, $body){
     global $chunks;
     $chunks->insert(array("owner" => new MongoId($owner),
                           "modification" => $modification,
                           "number" => $number,
                           "body" => $body
     )); 
}

function getChunkForRecover($owner, $modification, $number){
    global $chunks;
    $chunk = $chunks->findOne(array("owner" => new MongoId($owner),
                           "modification" => $modification,
                           "number" => $number));
    if($chunk)
        return $chunk["body"];
    
}

function deleteChunkRecover($owner, $modification, $number){
    global $chunks;
    $chunk = $chunks->remove(array("owner" => new MongoId($owner),
                                   "modification" => $modification,
                                   "number" => $number));
}
?>