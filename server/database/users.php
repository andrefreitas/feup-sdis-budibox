<?php
/* -----------------------
 * Users collection
* -----------------------
* JSON document schema:
* {
*     name : <user name>,
*     email : <email address>,
*     password : <sha256 password>,
*     key : <md5 private key that is used to encrypt>,
*     registrationDate : <registration date>
* }
*/
require_once("connection.php");
require_once("files.php");
/*
 * Users manipulation functions
 */
$users = $db->users; #collection

function createUser($name, $email, $password){
    global $users;
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
    global $users;
    return $users->findOne(array("email" => $email));
}

function userExists($email){
    return count(getUser($email)) > 0;
}

function getUserKey($email){
    global $users;
    $data = $users->findOne(array("email" => $email),array("key" => true));
    return $data["key"];
}

function checkUserLogin($email, $password){
    global $users;
    $password = hash('sha256', $password);
    $data = $users->findOne(array("email" => $email,"password" => $password));
    return count($data)>0;
}

function updateUserEmail($oldEmail, $newEmail){
    global $users;
    $newdata = array('$set' => array("email" => $newEmail));
    $users->update(array("email" => $oldEmail), $newdata);
    updateFilesUser($oldEmail, $newEmail);
}

function updateUserPassword($email, $password){
    global $users;
    $password = hash('sha256', $password);
    $newdata = array('$set' => array("password" => $password));
    $users->update(array("email" => $email), $newdata);
}

function deleteUser($email){
    global $users;
    $users->remove(array("email" => $email));
    removeFilesFromUser($email);
}

?>