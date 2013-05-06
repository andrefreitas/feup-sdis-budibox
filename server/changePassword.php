<?php
    chdir("common");
    require_once("init.php");
    chdir("../database");
    require_once("users.php");
    if(isset($_GET["key"]) and confirmationKeyIsValid((string)$_GET["key"])){
        $key = (string)$_GET["key"];
        $smarty->assign('key', $key);
        $smarty->display('changePassword.tpl');
    }
    else{
        header("Location: index.php");
    }
    
   
?>