<?php
/* -----------------------
 * Request collection
* -----------------------
* JSON document schema:
* {
*     action : <action name>,
*     who : <the computer id responsible for this action>,
*     ( <param> : <value> ) *
* }
*/
require_once("connection.php");

/* Set requests collection */
$requests = $db->requests;

/**
 * Request a Store Chunk
 * @param $who the computer id responsible for this action
 * @param $file_id the _id from the document in the files collection
 * @param $modification the sha256 modification of the file
 * @param $chunkNumber the chunk number (0-n)
 */
function requestStoreChunk($who, $fileId, $modification, $chunkNumber){
    global $requests;
    $requests->insert(array("action" => "storeChunk",
                            "who" => new MongoId($who),
                            "fileId" => $fileId,
                            "modification" => $modification,
                            "chunkNumber" => $chunkNumber
                      ));
}

/**
 * Get computer requests
 */
function getComputerRequests($computerId){
    global $requests;
    $cursor = $requests->find(array( "who" => new MongoId($computerId)));
    $data = array();
    foreach($cursor as $doc){
        $data[] = $doc;
    }
    return $data;
}
?>