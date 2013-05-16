<?php
/* -----------------------
 * Chunks collection
 * -----------------------
 * JSON document schema:
 * {
 *     user_id: <ObjectId()>,
 *     file_id : <ObjectId()>,
 *     modification: <sha256 modification>,
 *     number : <chunk number>
 *     body : <chunk body>,
 * }
 */
require_once("connection.php");

/* Set chunks collection */
$chunks = $db->chunks; 

function addChunk($userId, $fileId, $modification, $number, $body){
    global $chunks;
    $chunks->insert(array("user_id" => new MongoId($userId),
                          "file_id" => new MongoId($fileId),
                          "modification" => $modification,
                          "number" => $number,
                          "body" => $body   
                    ));
}
?>