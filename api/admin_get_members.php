<?php

if (ClientRequest::getRequestAuth()) {
    $token = ClientRequest::getRequestAuth();
    if(Access_Token::isAliveToken($token)){
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
$respone['status'] = $status;
$respone['message'] = $message;

?>
