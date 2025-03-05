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
    echo '<pre>';
    print_r(Category::getAllCategories());
    echo '</pre>';
    $category_test = new Category('Test Category x');
    if ($category_test->isCreated()) {
        echo 'created';
    }else{
        echo 'not created';
    }
    print_r(__PDO__->pdo_query('select isProductAvailable(99) as found',false));
?>