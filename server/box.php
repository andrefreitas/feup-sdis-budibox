<?php
    chdir("common");
    require_once("init.php");
    chdir("../database");
    require_once("files.php");
    require_once("users.php");
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
    $user = getUser($_SESSION["email"]);
    $spaceUsed = $user["space"]["used"] / $user["space"]["limit"] *100;
    $spaceUsed = round($spaceUsed, 2);
    $spaceLimit = $user["space"]["limit"] / (1024*1024*1024);
    $spaceLimit = round($spaceLimit, 2);
    $name = $user["name"];
    $email = $user["email"];
    $spaceOffer = $user["space"]["offer"];
    $spaceOffer = $spaceOffer / (1024*1024*1024);
    $smarty->assign("dir", $dir);
    $smarty->assign("files", $files);
    $smarty->assign("deletedFiles", $deletedFiles);
    $smarty->assign("pendingFiles", $pendingFiles);
    $smarty->assign("directories", $directories);
    $smarty->assign("spaceUsed",  $spaceUsed);
    $smarty->assign("spaceLimit",  $spaceLimit);
    $smarty->assign("name",  $name);
    $smarty->assign("email",  $email);
    $smarty->assign("spaceOffer",  $spaceOffer);
    
    /* Display */
    $smarty->display("box.tpl");
   
?>