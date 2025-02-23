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
            // User::getAllMembers($respone, $status, $message );
            
            // updateUserInfoById
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                $rawPostData = file_get_contents("php://input");
                if(Logic_Function::isValidJson($rawPostData)){
                    $json = json_decode($rawPostData, true);

                    if (Logic_Function::isFound($json['id']) && is_numeric($json['id']) && $json['id'] > 0){
                        if (User::updateUserInfoById($json['id'], $json,$message)){
                            $status = 'success';
                            
                        }
                    }else{
                        $message = 'invalid data.';
                    }
                }else{
                    $message = 'invalid request body format.';
                }
            }else{
                $message = 'BAD REQUEST METHOD';
            }

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
