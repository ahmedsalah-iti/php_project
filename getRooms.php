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
    $res['data'] = __PDO__->pdo_select('Room');
    if (Logic_Function::isFound($res)){
        $respone = $res;
        $status = 'success';
    }
    $respone['message'] = $message;
    $respone['status'] = $status;
    echo json_encode($respone ,JSON_PRETTY_PRINT);

?>