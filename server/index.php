<?php
    chdir("common");
    require_once("init.php");
    if(isset($_SESSION["_id"]))
        header("Location: explorer.php");
    if(isset($_GET["welcome"]))
        $smarty->assign('welcome', $_GET["welcome"]);
    $smarty->display('home.tpl');
   
?>