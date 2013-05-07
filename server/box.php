<?php
    chdir("common");
    require_once("init.php");
    /* Redirect to index if is guest */
    if(!isset($_SESSION["_id"]))
        header("Location: index.php");
    
    /* Process files from directory */
    $dir = "/";
    if(isset($_GET["dir"]))
        $dir = $_GET["dir"];
    $files = array("loja.txt","correio.doc","DCIM-1233.jpg","notes.zip");
    $directories = array("fotos", "junk", "movies");
    $smarty->assign("dir", $dir);
    $smarty->assign("files", $files);
    $smarty->assign("directories", $directories);
    /* Display */
    $smarty->display("box.tpl");
   
?>