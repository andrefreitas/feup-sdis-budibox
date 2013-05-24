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
 * Computer confirms that did the storechunk request and its id is removed from the list
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

/**
 * Computer confirms that deleted that file modification
 */
function deleteFileModificationDone($computerId, $modification){
    global $requests;
    $computerId = new MongoId($computerId);
    $requests->update(
                      array("action" => "deleteFile", "modification" => $modification),
                      array('$pull' => array("who" => $computerId))
                     );

    // If request is completely done
    if(deleteFileModificationIsComplete($modification)){
        $requests->remove(array("action" => "deleteFile", "modification" => $modification));
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

function deleteFileModificationIsComplete($modification){
    global $requests;
    return $requests->findOne(array("action" => "deleteFile", "modification" => $modification, "who" => array()));
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

function requestFileDelete($actualModification, $who){
    global $requests;
    $requests->insert(array("action" => "deleteFile",
                            "modification" => $actualModification,
                            "who" => $who));

}

/* Request file restore */

function requestGiveChunk($modification, $chunkNumber, $who, $owner){
    global $requests;
    $requests->insert(array("action" => "giveChunk",
            "modification" => $modification,
            "chunkNumber" => $chunkNumber,
            "who" => $who,
            "owner" => new MongoId($who)
    ));

}

function removeGiveChunkRequest($modification, $chunkNumber, $owner){
    global $requests;
    $requests->remove(array("action" => "giveChunk",
                            "modification" => $modification,
                            "chunkNumber" => $chunkNumber,
                            "owner" => new MongoId($owner)
    ));
}

function toMongoId($item){
    return new MongoId($item);
}

function requestRestoreFileModification($modification, $owner){
    global $requests;
    // $chunksComputers = getOnlineComputersFromChunks($modification); // Online Computers
    $chunksComputers = getComputersFromChunks($modification); // All Computers
    for($i = 0; $i < count($chunksComputers) ; $i++){
        $chunkNumber = $i;
        $computersIds = $chunksComputers[$i];
        $computersIds = array_map("toMongoId", $computersIds);
        requestGiveChunk($modification, $chunkNumber, $computersIds, $owner);
    }
}

function requestRecoverChunk($owner, $modification, $number, $body){
    global $requests;
    $filePath = getFilePathByModification($modification);
    $requests->insert(array("action" => "recoverChunk",
                            "modification" => $modification,
                            "number" => $number,
                            "who" => new MongoId($owner),
                            "path" => $filePath
    ));
    storeChunkForRecover($owner, $modification, $number, $body);
}

function restoreFileIsDone($owner, $modification){
    global $requests;
    $giveChunk = $requests->findOne(array("action" => "giveChunk",
                                          "modification" => $modification,
                                          "owner" => new MongoId($owner)
    ));
    
    $recoverChunk = $requests->findOne(array("action" => "recoverChunk",
                                             "modification" => $modification,
                                             "who" => new MongoId($owner)
    ));
    // True if there is no givechunk or recover chunk pending
    return !($giveChunk or $recoverChunk);
}

function confirmRecoverChunk($owner, $modification, $chunkNumber ){
    global $requests;
    $requests->remove(array("action" => "recoverChunk",
                            "modification" => $modification,
                            "number" => $chunkNumber,
                            "who" => new MongoId($owner)
    ));
}
?>