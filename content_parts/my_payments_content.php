<?php

$orders = Order::getAllOrdersByUserId(1);

$results = array();
foreach ($orders as $order) 
{
    $order_id = $order["id"];
    $payments = Payment::getAllPaymentsByOrderId($order_id);

    foreach ($payments as $payment) 
    {
        $results["$order_id"][] = $payment;
    }
}

echo "<pre>";
print_r($results);
echo "</pre>";