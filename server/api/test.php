<?php
header('Content-type: application/json');
    if(isset($_POST["data"])){
        echo json_encode(array("result" => $_POST["data"]));
    }
?>