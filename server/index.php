<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
    chdir("common");
    require_once("init.php");
    $smarty->display('home.tpl');
?>