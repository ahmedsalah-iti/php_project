<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    require_once('database.php');
    require_once('User.php');
    require_once('access_token.php');
    require_once('functions.php');
    header('Content-Type: application/json');
    $respone = array();
    $status = 'failed';
    $message = '';
    User::RegisterNewUser($respone, $status, $message);
    

    $respone['message'] = $message;
    $respone['status'] = $status;
    sleep(seconds: 1);
    echo json_encode($respone ,JSON_PRETTY_PRINT);
?>