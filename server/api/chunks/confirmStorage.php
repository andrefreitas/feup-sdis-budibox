<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("files.php");
chdir("..");
require_once("configuration.php");

/*
 * Example: api/chunks/confirmStorage.php?fileId=5194dbafbd112c981a000017&apikey=12&number=20&computerId=1234
 */
if (isset($_GET['apikey']) and
    isset($_GET['fileId']) and
    isset($_GET['number']) and
    isset($_GET['computerId'])
){
    $auth = (string) $_GET['apikey'];
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $fileId = (string) $_GET['fileId'];
        $number = (string) $_GET['number'];
        $computerId = (string) $_GET['computerId'];
        addComputerToChunk($fileId, $number, $computerId);
        echo json_encode(array("result" => "ok"));
    }

}else{
    echo json_encode(array("result" => "missingParams"));
}
?>