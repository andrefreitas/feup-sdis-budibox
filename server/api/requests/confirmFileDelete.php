<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("requests.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Confirm's that a file modification has been deleted
 * PARAMETERS: api/requests/confirmFileDelete.php <apikey> <computerId> <modification>
 * NOTES: The fileId is the "_id" from the files collection.
 */

if (isset($_GET['apikey']) and
    isset($_GET['computerId']) and 
    isset($_GET['modification'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $computerId = (string) $_GET['computerId'];
        $modification = (string) $_GET['modification'];
        deleteFileModificationDone($computerId, $modification);
        echo json_encode(array("result" => "ok"));
    }

}else{
    echo json_encode(array("result" => "missingParams"));
}
?>