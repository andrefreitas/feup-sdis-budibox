<?php
    require_once("../configuration.php");
    try {
        $connection = new MongoClient("mongodb:// ". $members,
        array("replicaSet" => $replicaSet, 
              "db" => $database, 
              "username" => $databaseUser, 
              "password" => $databasePassword,
              "timeout" => 6000 ));
        $connection->setReadPreference(MongoClient::RP_PRIMARY_PREFERRED , array());
        $db = $connection->selectDB($database);
    } catch (Exception $e) {
        echo "Failed connecting to mongodb: " . $e->getMessage();
    }
    
?>