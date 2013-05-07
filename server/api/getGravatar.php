<?php
    header('Content-type: application/json');
    if( isset($_GET['email']) and isset($_GET['default']) and isset($_GET['size'])){
        $url =  "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $_GET['email'] ) ) ) . "?d=" . urlencode( $_GET['default'] ) . "&s=" . $_GET['size'];
        echo json_encode(Array("url" => $url));
    }
    
    
?>