<?php
header('Content-type: application/json');
chdir("../..");
chdir("database");
require_once("users.php");
chdir("..");
require_once("configuration.php");
chdir("common");
require_once("init.php");
/**
 * DESCRIPTION: Gets the user data
 * PARAMETERS: api/users/getUser.php <apikey> <email>
 */
if (isset($_GET['email'])
){
    // --> begin authentication
    $havePermission = false;
    if(isset($_GET['apikey'])){
        $auth = (string) $_GET['apikey'];
        $havePermission = $auth==$apikey;
    } else {
        $havePermission = ($_SESSION["email"] == $_GET['email']);
    }
    // --> end authentication
    
    if (!$havePermission){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $email = (string) $_GET['email'];
        $user = getUser($email);
        echo json_encode(array("result" => "ok", "user" => $user));
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>