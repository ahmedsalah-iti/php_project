<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    
    require_once "classes/Logic_Function.php";
    require_once "classes/Database.php";
$db_cfg = new DB_CFG("35.157.233.91","cafeteria","php_tester","123");
$db = new Database($db_cfg);
define('__PDO__',$db);
    spl_autoload_register(function ($class) {
        $file = "classes/" . $class .".php";
        if (file_exists($file) && $class != 'index') {
            require_once $file;
        }
    });
$action = isset($_GET['action']) ? $_GET['action'] : 'login';
// if (!isset($_GET['loop']) || $_GET['loop'] != 'off') {
//     header('location: ./'.$action.'');
//     // echo '.d..d.d.d.d.d.d.d.'.$action;
// }else{
//     // echo 'ssssss.s.s.s.'.$action;
// }

$pageMap = [
    'register' => [
        'title' => 'Register',
        'content' => 'register_content.php',
        'js' => 'register.js',
        'header' => 'header.php'
    ],
    'products' => [
        'title' => 'Products',
        'content' => 'products_orders_content.php',
        'js' => 'products_orders.js',
        'header' => 'navbar.php',
        'class' => 'products-orders'
    ],
    'login' => [
        'title' => 'Login',
        'content' => 'login_content.php',
        'js' => 'login.js',
        'header' => 'header.php'
    ],
    'dashboard' => [
        'title' => 'Dashboard',
        'content' => 'dashboard_content.php',
        'js' => 'dashboard.js',
        'header' => 'navbar.php'
    ],
    'change_password' => [
        'title' => 'Change Password',
        'content' => 'change_password_content.php',
        'js' => 'change_password.js',
        'header' => 'navbar.php'
    ],
    'admin' => [
        'title' => 'Admin Panel',
        'content' => 'admin_content.php',
        'js' => 'admin.js',
        'header' => 'navbar.php'
    ],
    'orders' =>[
        'title' => 'Orders',
        'content' => 'my_orders_content.php',
        'js' => 'order.js',
        'header' => 'navbar.php'
    ],
    'payments' =>[
        'title' => 'Payments',
        'content' => 'my_payments_content.php',
         'js' => 'payment.js',
        'header' => 'navbar.php'
    ],
];

$pageData = isset($pageMap[$action]) ? $pageMap[$action] : $pageMap['login'];
$pageTitle = $pageData['title'];

include_once './html_parts/head.php';
?>
<body>
    <?php include_once "./html_parts/{$pageData['header']}"; ?>
    <main <?php echo isset($pageData['class']) ? "class=\"{$pageData['class']}\"" : ''; ?>>
        <?php include_once "./content_parts/{$pageData['content']}"; ?>
    </main>
    <?php 
    include_once './html_parts/footer.php'; 
    include_once './html_parts/notifications.php'; 
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/utils.js"></script>
    <script src="./assets/js/<?php echo $pageData['js']; ?>"></script>
</body>
</html>