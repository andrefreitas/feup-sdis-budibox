<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("requests.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Checks if restore file is done
 * PARAMETERS: api/files/restoreFileIsDone.php <apikey> <computerId> <modification>
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
        $owner = (string) $_GET['computerId'];
        $modification = (string) $_GET['modification'];
        echo json_encode(array("result" => "ok", "isDone" => restoreFileIsDone($owner, $modification)));
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>