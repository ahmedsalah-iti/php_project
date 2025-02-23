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
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $rawPostData = file_get_contents("php://input");
    if(Logic_Function::isValidJson($rawPostData)){
        $json = json_decode($rawPostData, true);
        if(Logic_Function::isFound($json['type'])){
            if ($json['type'] === 'email'){
                if(Logic_Function::isFound($json['pass']) && Logic_Function::isValidEmailKey($json['email'])){
                    $email = $json['email'];
                    $pass = $json['pass'];
                    $login = User::LoginWithEmail( $email, $pass );
                    if(Logic_Function::isFound($login)){
                        $respone['data'] = $login;
                        $status = 'success';
                        $message = 'logged in successfuly.';
                    }else{
                        $message = 'wrong email/pass.';
                    }
                }else{
                    $message = 'missing valid email/pass.';
                }
            }elseif ($json['type'] === 'token'){
                if(Logic_Function::isFound($_SERVER['HTTP_AUTHORIZATION'])){
                    $token = $_SERVER['HTTP_AUTHORIZATION'];
                    $login = User::loginWithToken(($token));
                    if(Logic_Function::isFound($login)){
                        $respone['data'] = $login;
                        $message = 'logged in successfuly.';
                    }else{
                        $message = 'wrong / expired token.';
                    }
                
                }else{
                    $message = 'missing HTTP_AUTHORIZATION.';
                }
            }else{
                $message = 'unsupported login type.';
            }
        }else{
            $message = 'invalid login type.';
        }
    }else{
        $message = 'invalid request body format.';
    }
}else{
    $status = 'failed';
    $message = 'BAD REQUEST METHOD';
}
$respone['message'] = $message;
$respone['status'] = $status;
sleep(seconds: 1);
echo json_encode($respone ,JSON_PRETTY_PRINT);
?>