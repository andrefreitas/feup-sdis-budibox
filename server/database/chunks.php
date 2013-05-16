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

/* Set chunks collection */
$chunks = $db->chunks; 

function addChunk($fileId, $modification, $number, $body){
    global $chunks;
    $chunks->insert(array("file_id" => new MongoId($fileId),
                          "modification" => $modification,
                          "number" => $number,
                          "body" => $body   
                    ));
}
?>