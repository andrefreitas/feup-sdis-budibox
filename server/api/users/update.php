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
 * DESCRIPTION: Updates the user
 * PARAMETERS: api/users/update.php [<apikey>] <email> [<password>] [<offer>] [<name>] [<newEmail>]
 */
if (isset($_GET['email'])
){
    // --> begin authentication
    $havePermission = false;
    if(isset($_GET['apikey'])){
        $auth = (string) $_GET['apikey'];
        $havePermission = $auth==$apikey;
    }else{
        $havePermission = ($_SESSION["email"] == $_GET['email']);
    }
    // --> end authentication
    
    if (!$havePermission){
        echo json_encode(array("result" => "permissionDenied"));
    }
    else {
        $email = (string) $_GET['email'];
        $data = array();
        
        if(isset($_GET['name'])){
            $_SESSION["name"] = $_GET['name'];
            $data["name"] = (string) $_GET['name'];
        }
        
        if(isset($_GET['password']))
            $data["password"] = (string) $_GET['password'];
        
        if(isset($_GET['offer']))
            $data["offer"] = intval($_GET['offer']) * (1024*1024*1024);
        
        if(isset($_GET['newEmail'])){
            $_SESSION["email"] = $_GET['newEmail'];
            $data["email"] = (string) $_GET['newEmail'];
        }
        updateUser($email, $data);
        echo json_encode(array("result" => "ok"));
    }


}else{
    echo json_encode(array("result" => "missingParams"));
}
?>