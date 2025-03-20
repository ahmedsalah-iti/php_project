<?php
/*
{
    "payment_id": 1
}
*/
if (ClientRequest::isGetRequest() || ClientRequest::isPostRequest()){
    if (ClientRequest::getRequestAuth()) {
        $token = ClientRequest::getRequestAuth();
        $login = User::loginWithToken(($token));
        if(Logic_Function::isFound($login) && Logic_Function::isFound($login['id'])){
            $user_id = $login['id'];
            if (ClientRequest::isPostRequest()){
                //get the details of one of my payments by payment_id.
                $RequestData = ClientRequest::getRequestData();
                if (Logic_Function::isValidJson($RequestData) ){
                    $ReqData = json_decode($RequestData,true);
                    if (Logic_Function::isFound($ReqData['payment_id']) && Logic_Function::isValidId($ReqData['payment_id'])){
                        $payment_id = $ReqData['payment_id'];
                        if (Payment::isUserHasAccessToPayment(intval($payment_id),$user_id)){
                            $paymentData = Payment::getPaymentById(intval($payment_id));
                            if($paymentData){
                                $respone['data'] = $paymentData;
                                $message = "successfuly received your payment's data.";
                                $status = "success";
                            }else{
                                $message = 'unknown error , maybe not found payment id';
                            }
                        }else{
                            $message = 'you dont have permission to access this payment.';
                        }

                    }else{
                        $message = 'invalid payment id';
                    }
            
                }else{
                    $message = 'invalid json data';
                }
            }else{
                //get all of my payments.
                $myPayments = Payment::getAllPaymentsByUserId(intval($user_id));
                if($myPayments){
                    $respone['data'] = $myPayments;
                    $message = "successfuly received your payments.";
                    $status = "success";
                }else{
                    $message = 'not found user.';
                }
            }
        }else{
            $message = 'invalid access token / unauthorized';
        }
    }else{
        $message = 'unauthorized';
    }
}else{
    $message = 'BAD REQUEST METHOD';
}
$respone['message'] = $message;
$respone['status'] = $status;
?>