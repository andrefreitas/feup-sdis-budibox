<?php
    require_once("../configuration.php");
    $connection = new MongoClient("mongodb:// ". $members . "/?replicaSet=" . $replicaSet);
    $db = $connection->selectDB($database);
    
?>