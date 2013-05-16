<?php
/* -----------------------
 * Computers collection
 * -----------------------
 * JSON document schema:
 * {
 *     user : <user email>,
 *     name : <computer name>,
 *     setupDate : <computer setup date>,
 *     status : <status of the computer>,
 *     lastTimeAlive : <last time the computer was online>  
 * }
 */
require_once("connection.php");

/* Set chunks collection */
$computers = $db->chunks; 
?>