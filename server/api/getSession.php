<?php
    header('Content-type: application/json');
    session_start();
    echo json_encode($_SESSION);
?>