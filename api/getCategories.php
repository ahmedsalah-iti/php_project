<?php

if (ClientRequest::getRequestAuth()) {
    $token = ClientRequest::getRequestAuth();
    if(Access_Token::isAliveToken($token)){
        $categories = Category::getAllCategories();
        if (Logic_Function::isFound($categories) && count($categories) > 0){
            $respone['all_categories_data'] = $categories;
            $message = "all Categories loaded successfuly.";
            $status = "success";
        }else{
            $message = 'could not find any categories.';
        }
    }else{
        $message = 'invalid access token / unauthorized';
    }
}else{
    $message = 'unauthorized';
}
$respone['message'] = $message;
$respone['status'] = $status;
?>
