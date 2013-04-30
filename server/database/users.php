<?php
require_once("connection.php");
/*
 * Users manipulation functions
 */
function createUser($name, $email, $password){
    global $db;
    $users = $db->users;
    $password = hash('sha256', $password);
    $key = substr(str_shuffle(md5(time())),0,10);
    $users->insert(array("name" => $name, 
                         "email" => $email, 
                         "password" => $password,
                         "key" => $key,
                         "registrationDate" => new MongoDate()
                   ));
}

function getUser($email){
    global $db;
    $users = $db->users;
    return $users->findOne(array("email" => $email));
}

function userExists($email){
    return count(getUser($email)) > 0;
}

function getUserKey($email){
    global $db;
    $users = $db->users;
    $data = $users->findOne(array("email" => $email),array("key" => true));
    return $data["key"];
}

function checkUserLogin($email, $password){
    global $db;
    $users = $db->users;
    $password = hash('sha256', $password);
    $data = $users->findOne(array("email" => $email,"password" => $password));
    return count($data)>0;
}

function updateUserEmail($oldEmail, $newEmail){
    global $db;
    $users = $db->users;
    $newdata = array('$set' => array("email" => $newEmail));
    $users->update(array("email" => $oldEmail), $newdata);
}

function updateUserPassword($email, $password){
    global $db;
    $users = $db->users;
    $password = hash('sha256', $password);
    $newdata = array('$set' => array("password" => $password));
    $users->update(array("email" => $email), $newdata);
}

function deleteUser($email){
    global $db;
    $users = $db->users;
    $users->remove(array("email" => $email));
}

?>