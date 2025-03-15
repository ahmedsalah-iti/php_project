<?php
if (ClientRequest::getRequestAuth()) {
    $token = ClientRequest::getRequestAuth();
    if(Access_Token::isAliveToken($token)){
        $Products = Product::getAllProducts();
        if (Logic_Function::isFound($Products) && count($Products) > 0){
            $respone['all_products_data'] = $Products;
            $message = "all Products loaded successfuly.";
            $status = "success";
        }else{
            $message = 'could not find any Products.';
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
