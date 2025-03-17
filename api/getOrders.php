<?php
/*
{
    "order_id": 1
}
*/
if (ClientRequest::isGetRequest() || ClientRequest::isPostRequest()){
    if (ClientRequest::getRequestAuth()) {
        $token = ClientRequest::getRequestAuth();
        $login = User::loginWithToken(($token));
        if(Logic_Function::isFound($login) && Logic_Function::isFound($login['id'])){
            $user_id = $login['id'];
            if (ClientRequest::isPostRequest()){
                //get the details of one of my orders by order_id.
                $RequestData = ClientRequest::getRequestData();
                if (Logic_Function::isValidJson($RequestData) ){
                    $ReqData = json_decode($RequestData,true);
                    if (Logic_Function::isFound($ReqData['order_id']) && Logic_Function::isValidId($ReqData['order_id'])){
                        $order_id = $ReqData['order_id'];
                        if (Order::isUserHasAccessToOrder(intval($order_id),$user_id)){
                            $orderData = Order::getOrderDataById(intval($order_id));
                            if($orderData){
                                $respone['data'] = $orderData;
                                $message = "successfuly received your order's data.";
                                $status = "success";
                            }else{
                                $message = 'unknown error , maybe not found order id';
                            }
                        }else{
                            $message = 'you dont have permission to access this order.';
                        }

                    }else{
                        $message = 'invalid order id';
                    }
            
                }else{
                    $message = 'invalid json data';
                }
            }else{
                //get all of my orders.
                $myOrders = Order::getAllOrdersByUserId(intval($user_id));
                if($myOrders){
                    $respone['data'] = $myOrders;
                    $message = "successfuly received your orders.";
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