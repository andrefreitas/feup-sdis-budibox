<?php
    chdir("common");
    require_once("init.php");
    if(!isset($_SESSION["_id"]))
        header("Location: index.php");
    $smarty->display("box.tpl");
   
?>