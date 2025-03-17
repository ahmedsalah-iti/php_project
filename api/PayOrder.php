<?php
/*
{"method":"cash","order_id":29}
{"method":"online","credit_card":{"number":"123","expiry":"1234","cvv":"1234","name":"asd 2213"},"order_id":29}
{"method":"delivery","order_id":29}
*/
if (ClientRequest::isPostRequest()) {
    if (ClientRequest::getRequestAuth()) {
        $token = ClientRequest::getRequestAuth();
        $login = User::loginWithToken($token);
        if (
            Logic_Function::isFound($login) &&
            Logic_Function::isFound($login["id"])
        ) {
            $user_id = $login["id"];
            $RequestData = ClientRequest::getRequestData();
            if (Logic_Function::isValidJson($RequestData)) {
                $ReqData = json_decode($RequestData, true);
                if (
                    Logic_Function::isFound($ReqData["method"]) &&
                    Logic_Function::isFound($ReqData["order_id"]) &&
                    Payment::isValidMethod($ReqData["method"])
                ) {
                    $paymentMethod = $ReqData["method"];
                    $order_id = $ReqData["order_id"];
                    if (Order::isOrderFoundInDB($order_id)) {
                        if (
                            Order::isUserHasAccessToOrder($order_id, $user_id)
                        ) {
                            $Payment = new Payment($order_id, $paymentMethod);
                            $payment_id = $Payment->getId();
                            if ($Payment->isCreated()) {
                                if ($Payment->isCompleted()) {
                                    $respone['payment_id'] = $payment_id;
                                    // paid OR  pay on delivery
                                    $status = "success";
                                    if (Order::isOrderCompleted($order_id)) {
                                        $message = "order is paid successfuly.";
                                        
                                    } else {
                                        $message =
                                            "Payment Created successfuly , Order will be & reviewed and confirmed Manually by the Admin.";
                                    }
                                } else {
                                    switch ($Payment->getMethod()) {
                                        case "cash":
                                            $message =
                                                "you dont have enough money to pay.";
                                            break;
                                        case "delivery":
                                            $message =
                                                "you are already have enough money to pay , please choose CASH.";
                                            break;
                                        case "online":
                                            $message = "Credit card declined";
                                            break;
                                    }
                                }
                            } else {
                                $message =
                                    "something went wrong while creating the payment or Order already paid.";
                            }
                        } else {
                            $message =
                                "you dont have permission to pay for this order.";
                        }
                    } else {
                        $message = "not found order_id";
                    }
                } else {
                    $message = "missing method / order_id";
                }
            } else {
                $message = "invalid json data";
            }
        } else {
            $message = "invalid access token / unauthorized";
        }
    } else {
        $message = "unauthorized";
    }
} else {
    $message = "BAD REQUEST METHOD";
}
$respone['message'] = $message;
$respone['status'] = $status;
?>