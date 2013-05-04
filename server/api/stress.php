<?php
header('Content-type: application/json');
chdir("..");
chdir("database");
require_once("users.php");
if(isset($_GET["n"])){
    $stress = $db->stress;
    for($i=0; $i<(int)$_GET["n"]; $i++){
        $stress->insert(array("val" => substr(str_shuffle(md5(time())),0,20)));
        
    }
}
?>