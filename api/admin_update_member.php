<?php
if (ClientRequest::getRequestAuth()) {
    $token = ClientRequest::getRequestAuth();
    if(Access_Token::isAliveToken($token)){
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

$respone['status'] = $status;
$respone['message'] = $message;
?>
