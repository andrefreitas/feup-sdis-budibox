<?php
chdir("..");
chdir("database");
require_once("users.php");
set_time_limit(0);
if(isset($_GET["n"]) and isset($_GET["size"])){
    $stress = $db->stress;
    $Length = ((int)$_GET["size"])/10;
    $str= "";
    for($i=0; $i<$Length; $i++){
        $RandomString = substr(str_shuffle(md5(time())),0,10);
        $str = $str . $RandomString;
    }
    for($i=0; $i<(int)$_GET["n"]; $i++){
        $stress->insert(array("val" => $str));
        echo $i."<br/>";
    }
}
?>