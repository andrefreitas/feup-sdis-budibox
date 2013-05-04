<?php
    require_once("../configuration.php");
    try {
        $connection = new MongoClient("mongodb:// ". $members . "/?replicaSet=" . $replicaSet);
        $db = $connection->selectDB($database);
    } catch (Exception $e) {
        echo "Failed connecting to mongodb";
    }
    
?>