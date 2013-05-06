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
*     registrationDate : <registration date>,
*     status : <the user status>,
*     confirmationKey : <the confirmation key that is emailed>
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
    $confirmationKey = substr(str_shuffle(md5(time())),0,14);
    $users->insert(array("name" => $name, 
                         "email" => $email, 
                         "password" => $password,
                         "key" => $key,
                         "registrationDate" => new MongoDate(),
                         "status" => "inactive",
                         "confirmationKey" => $confirmationKey
                   ));
}

function getUserConfirmationKey($userEmail){
    global $users;
    $result = $users->findOne(array("email" => $userEmail),array("confirmationKey" => true));
    if(isset($result["confirmationKey"]))
        return $result["confirmationKey"];
}

function confirmationKeyIsValid($confirmationKey){
    global $users;
    $result = $users->findOne(array("confirmationKey" => $confirmationKey));
    return $result;
}

function activateUser($confirmationKey) {
    global $users;
    $newData = array('$set' => array("status" => "active"));
    $users->update(array("confirmationKey" => $confirmationKey), $newData);
}

function userIsActive($confirmationKey){
    global $users;
    $result = $users->findOne(array("confirmationKey" => $confirmationKey, "status" => "active"), array("_id" => true));
    return $result;
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

function updateUserPasswordByConfirmationKey($confirmationKey, $password){
    global $users;
    $password = hash('sha256', $password);
    $newdata = array('$set' => array("password" => $password));
    $users->update(array("confirmationKey" => $confirmationKey), $newdata);
}

function deleteUser($email){
    global $users;
    $users->remove(array("email" => $email));
    removeFilesFromUser($email);
}

?>