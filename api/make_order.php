<?php
/*
{
  "room_id": "9",
  "note": "tray asdasd asdasda as ",
  "products": [
    {
      "id": 2,
      "quantity": 3
    },
    {
      "id": 4,
      "quantity": 2
    },
    {
      "id": 7,
      "quantity": 1
    }
  ]
}
*/
if (ClientRequest::isPostRequest()){
    if (ClientRequest::getRequestAuth()) {
        $token = ClientRequest::getRequestAuth();
        $login = User::loginWithToken(($token));
        if(Logic_Function::isFound($login) && Logic_Function::isFound($login['id'])){
           $RequestData = ClientRequest::getRequestData();
           if (Logic_Function::isValidJson($RequestData) ) {
            $ReqData = json_decode($RequestData,true);
            if (Logic_Function::isFound($ReqData['products']) && is_array($ReqData['products'])  && count($ReqData['products']) > 0 && Logic_Function::isValidId($ReqData['room_id'])){
                $room_id = $ReqData['room_id'];
                $note = null;
                $products = $ReqData['products'];
                $user_id = $login['id'];
                if (Logic_Function::isFound($ReqData['note'])){
                    $note = $ReqData['note'];
                }
                $new_order = new Order($user_id,$room_id,$note);
                if ($new_order->isCreated()){
                    $order_id = $new_order->getId();
                    foreach ($products as $product){
                        if (Logic_Function::isValidId($product['id']) && Logic_Function::isValidId($product['quantity'])){
                            $product_id = $product['id'];
                            $product_quantity = $product['quantity'];
                            $new_item = new Order_Product($order_id,$product_id,$product_quantity);
                            
                        }
                    }
                    $status = "success";
                    $message = "Order placed successfully..";
                    $respone['order_id'] = $order_id;
                }else{
                    $message = 'failed to create order.';
                }

            }else{
                $message = 'missing json data';
            }
           }else{
            $message = 'invalid json data';
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