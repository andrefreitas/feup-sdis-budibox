<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
require_once("requests.php");
chdir("..");
require_once("configuration.php");

/**
 * DESCRIPTION: Gets the file id from the database
 * PARAMETERS: api/files/restoreFile.php <apikey> <computerId> <modification>
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
      if (modificationExists($modification)){
          requestRestoreFileModification($modification, $computerId);
          echo json_encode(array("result" => "ok"));
      }else{
          echo json_encode(array("result" => "invalidModification"));
      }
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>