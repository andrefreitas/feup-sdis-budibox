<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("chunks.php");
chdir("..");
require_once("configuration.php");
if (isset($_GET['apikey']) and
    isset($_GET['userId']) and 
    isset($_GET['fileId']) and 
    isset($_GET['modification']) and 
    isset($_GET['body']) and 
    isset($_GET['number'])
){
    if ($auth != $apikey){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else
        echo json_encode(array("result" => "ok"));
    
 
}else{
    echo json_encode(array("result" => "missingParams"));
}
?>