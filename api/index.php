<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    header("Content-Type: application/json");
    require_once "../classes/Logic_Function.php";
    // require_once "../classes/ClientRequest.php";
    require_once "../classes/Database.php";

    $db_cfg = new DB_CFG("35.157.233.91","cafeteria","php_tester","123");
    $db = new Database($db_cfg);
    define('__PDO__',$db);

    $respone = [];
    $status = "failed";
    $message = "";

    spl_autoload_register(function ($class) {
        $file = "../classes/" . $class .".php";
        if (file_exists($file) && $class != 'index') {
            require_once $file;
        }
    });

    $RequestMethod = ClientRequest::getRequestMethod();
    $RequestEndPoint = ClientRequest::getRequestEndPoint();
    $ApiFiles = [
        "admin/add_category" => "admin_add_category.php",
        "admin/add_product" => "admin_add_product.php",
        "admin/change_img" => "admin_change_img.php",
        "admin/update_member" => "admin_update_member.php",
        "admin/get_members" => "admin_get_members.php",
        "get_products" => "getProducts.php",
        "get_categories" => "getCategories.php",
        "get_rooms" => "getRooms.php",
        "login" => "login.php",
        "register" => "register.php",
        "upload" => "upload.php",
        "change_pass" => "change_pass.php",
        "make_order" => "make_order.php",
        "get_order" => "getOrders.php",
        "pay" => "PayOrder.php",
    ];

    if ($RequestEndPoint && isset($ApiFiles[$RequestEndPoint])) {
        require_once $ApiFiles[$RequestEndPoint];
    } else {
        $respone['status'] = 'failed';
        $respone['message'] = "Invalid API endpoint";
    }
    echo json_encode($respone ,JSON_PRETTY_PRINT);

?>