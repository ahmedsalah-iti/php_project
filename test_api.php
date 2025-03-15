<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    require_once('database.php');
    require_once('functions.php');
    require_once('User.php');
    require_once('access_token.php');
    require_once('Category.php');
    require_once('Product.php');
    require_once('Order.php');
    require_once('Order_Product.php');
    require_once('Wallet_Transaction.php');
    echo '<pre>';
    print_r(Category::getAllCategories());
    echo '</pre>';
    $category_test = new Category('Test Category x');
    if ($category_test->isCreated()) {
        echo 'created';
    }else{
        echo 'not created';
    }
    echo '<br>';
    // print_r(__PDO__->pdo_query('select isProductAvailable(99) as found',false));
    // $order = new Order(1,1,'add ketchup');
    // if ($order->isCreated()) {
    //     echo 'created order<br>';
    // }else{
    //     echo 'not created order';
    // }
    echo '<pre>';
    // print_r(Order::getAllOrders());
    // print_r(Order::getOrderDataById(4));
    // echo Order::getOrderTotalPrice(4);
    // if (Order::isOrderFoundInDB(4)) {
    //     echo 'found<br>';
    // }else{
    //     echo ' not found <br>';
    // }

    // if (Order::isUserHasAccessToOrder(4,2)) {
    //     echo 'yes<br>';
    // }else{
    //     echo ' no <br>';
    // }
    // echo '</pre>';

    // echo '<pre>';
    // print_r(Order::getAllOrdersByUserId(2));
    // echo '</pre>';
    
    // $item = new Order_Product(4,2,3);
    // if ($item->isCreated()) {
    //     echo 'created item<br>';
    // }else{
    //     echo 'not created item';
    // }

    
    // echo '<pre>';
    // print_r(Order::getOrderItems(4));
    // echo '</pre>';

    // echo '<pre>';
    // print_r(Order::getOrderTotalPrice(4));
    // echo '</pre>';



    // $wallet_transaction = new Wallet_Transaction(1,'add',9);
    // if ($wallet_transaction->isCreated()) {
    //     echo 'created wallet_transaction<br>';
    //     print_r($wallet_transaction->getTransactionData());
    // }else{
    //     echo 'not created wallet_transaction';
    // }

    // echo '<pre>';
    // print_r(Wallet_Transaction::getAllTransactions());
    // echo '</pre>';
?>