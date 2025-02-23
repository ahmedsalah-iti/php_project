<?php
ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
error_reporting(E_ALL);
require_once "database.php";
require_once "User.php";
require_once "access_token.php";
require_once "functions.php";
header("Content-Type: application/json");
$respone = [];
$status = "failed";
$message = "";
if (Logic_Function::isFound($_SERVER["HTTP_AUTHORIZATION"])) {
    $token = $_SERVER["HTTP_AUTHORIZATION"];
    if(Access_Token::isValidTokenSyntax($token)){
        if (User::isRealAdmin($token)){
            User::getAllMembers($respone, $status, $message );
        }else{
            $message = "this api is requiring admin permission to be accessed.";
        }
    }else{
        $message = 'invalid access token / unauthorized';
    }
}else{
    $message = 'unauthorized';
}

    




$respone['message'] = $message;
$respone['status'] = $status;
sleep(seconds: 1);
echo json_encode($respone ,JSON_PRETTY_PRINT);
?>
