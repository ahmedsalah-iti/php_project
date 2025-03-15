<?php
ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
error_reporting(E_ALL);
require_once "database.php";
require_once "functions.php";
require_once "User.php";
require_once "access_token.php";
require_once "Category.php";
require_once "Product.php";
header("Content-Type: application/json");
$respone = [];
$status = "failed";
$message = "";
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

    




$respone['message'] = $message;
$respone['status'] = $status;
sleep(seconds: 1);
echo json_encode($respone ,JSON_PRETTY_PRINT);
?>
