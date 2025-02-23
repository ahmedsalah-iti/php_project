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
User::fullTokenLoginCheck( $respone,$status, $message );
if ($status === "success"){
    $oldImgPath = $respone['data']['profile_img'];
    if(User::uploadProfileImgByEmail($respone['data']['email'],$oldImgPath)){
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
}
$respone["message"] = $message;
$respone["status"] = $status;
echo json_encode($respone, JSON_PRETTY_PRINT);
?>
