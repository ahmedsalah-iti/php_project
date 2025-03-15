<?php
if (ClientRequest::getRequestMethod() === 'POST'){
    $rawPostData = ClientRequest::getRequestData();
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
                        $respone['data']['balance'] = User::getBalanceById($login['id']);
                        // $respone['data']['balance'] = 2856.55;//edit l   ater
                        $status = 'success';
                        $message = 'logged in successfuly.';
                    }else{
                        $message = 'wrong email/pass.';
                    }
                }else{
                    $message = 'missing valid email/pass.';
                }
            }elseif ($json['type'] === 'token'){
                if(ClientRequest::getRequestAuth()){
                    $token = ClientRequest::getRequestAuth();
                    $login = User::loginWithToken(($token));
                    if(Logic_Function::isFound($login) && Logic_Function::isFound($login['id'])){
                        $respone['data'] = $login;
                        $message = 'logged in successfuly.';
                        $status = 'success';
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
$respone['status'] = $status;
$respone['message'] = $message;
?>