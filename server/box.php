<?php
    chdir("common");
    require_once("init.php");
    chdir("../database");
    require_once("files.php");
    /* Redirect to index if is guest */
    if(!isset($_SESSION["_id"]))
        header("Location: index.php");
    
    /* Process files from directory */
    $dir = "/";
    if(isset($_GET["dir"]))
        $dir = $_GET["dir"];
    
    $files = getDirectoryFiles($_SESSION["email"], $dir, "active");
    $deletedFiles = getDirectoryFiles($_SESSION["email"], $dir, "deleted");
    $pendingFiles = getDirectoryFiles($_SESSION["email"], $dir, "pending");
    $directories = getDirectoryFolders($_SESSION["email"], $dir);
    $smarty->assign("dir", $dir);
    $smarty->assign("files", $files);
    $smarty->assign("deletedFiles", $deletedFiles);
    $smarty->assign("pendingFiles", $pendingFiles);
    $smarty->assign("directories", $directories);
    /* Display */
    $smarty->display("box.tpl");
   
?>