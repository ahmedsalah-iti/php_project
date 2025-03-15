<?php
if (ClientRequest::getRequestAuth()) {
    $token = ClientRequest::getRequestAuth();
    if(Access_Token::isAliveToken($token)){
        if (User::isRealAdmin($token)){
            // User::getAllMembers($respone, $status, $message );
            
            if (Logic_Function::isFound($_POST['id']) && is_numeric($_POST['id'])){
                
                $userId = $_POST['id'];
            
                $oldImgPath = './uploads/empty.jpg';
            if(User::uploadProfileImgByUserId($userId,$oldImgPath)){
                if (Logic_Function::isFound($oldImgPath) && $oldImgPath != './uploads/empty.jpg'){
                    $status = "success";
                    $message = "image is uploaded successfuly.";
                    $respone['data']['profile_img'] = $oldImgPath;
                }else{
                    $status = "failed";
                    $message = "unknown error , something went wrong";
                }
            }else{
                $status = "failed";
                $message = "something went wrong , we couldn't upload the image";
            }
        }else{
            $message = 'missing user id.';
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
