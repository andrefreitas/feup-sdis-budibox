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
require_once("chunks.php");
require_once("files.php");

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
                            "who" => $who,
                            "fileId" => new MongoId($fileId),
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

/**
 * Computer confirms thar done the storechunkdone request and its id is removed from the list
 */
function storeChunkDone($computerId, $fileId, $modification, $chunkNumber){
    $computerId = new MongoId($computerId);
    $fileId = new MongoId($fileId);
    global $requests;
    $requests->update(array("action" => "storeChunk", 
                            "fileId" => $fileId, 
                            "modification" => $modification, 
                            "chunkNumber" => $chunkNumber),
                             array('$pull' => array("who" => $computerId)));
    
    // If request is completely done
    if(storeChunkIsComplete($fileId, $modification, $chunkNumber)){
        // Remove Request
        $requests->remove(array("action" => "storeChunk",
                                "fileId" => $fileId,
                                "modification" => $modification,
                                "chunkNumber" => $chunkNumber));
        // Delete Chunk 
        deleteChunk($fileId, $modification, $chunkNumber);
        
        // If all the chunks where stored
        if(!fileHaveStoreRequests($fileId, $modification)){
            setFileStatusById($fileId, "active");
        }
        
    }
}

function storeChunkIsComplete($fileId, $modification, $chunkNumber){
    global $requests;
    $fileId = new MongoId($fileId);
    return $requests->findOne(array("action" => "storeChunk",  
                                    "fileId" => $fileId,  
                                    "modification" => $modification, 
                                    "chunkNumber" => $chunkNumber,
                                    "who" => array()
    ));
    
}

function fileHaveStoreRequests($fileId, $modification){
    global $requests;
    $request = new MongoId($fileId);
    return $requests->findOne(array("action" => "storeChunk",
            "fileId" => $fileId,
            "modification" => $modification
    ));
    return $request;
}
?>