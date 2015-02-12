<?php
require_once("../configuration.php");
  try {
    /*
    $connection = new MongoClient("mongodb:// ". $members,
    array("replicaSet" => $replicaSet,
    "db" => $database,
    "username" => $databaseUser,
    "password" => $databasePassword,
    "timeout" => 6000 ));
    $connection->setReadPreference(MongoClient::RP_PRIMARY_PREFERRED , array());
    */
    $config = array("db" => $database,
    "username" => $databaseUser,
    "password" => $databasePassword);
    //$connection = new MongoClient("mongodb://" . $databaseServer, $config );
    $connection = new MongoClient();
    $db = $connection->selectDB($database);
  } catch (Exception $e) {
    echo "Failed connecting to mongodb: " . $e->getMessage();
  }

?>
