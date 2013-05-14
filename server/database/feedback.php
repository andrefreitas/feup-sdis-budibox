<?php
/* -----------------------
 * Computers collection
* -----------------------
* JSON document schema:
* {
*     user : <user email>,
*     message : <message>,
*     date : <setup date>,
* }
*/
require_once("connection.php");

/* Set computers collection */
$feedbacks = $db->feedbacks;

function addFeedback($user, $message){
    global $feedbacks;
    $feedbacks->insert(array("user" => $user, 
                             "message" => $message,
                             "date" => new MongoDate()
                       ));
}
?>