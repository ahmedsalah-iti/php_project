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
sleep(seconds: 1);
echo json_encode($respone ,JSON_PRETTY_PRINT);
?>
