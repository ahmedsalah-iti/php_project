<?php
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
$respone["status"] = $status;
$respone["message"] = $message;
?>
