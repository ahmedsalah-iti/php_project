<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once('database.php');
require_once('User.php');
require_once('access_token.php');


try{
// $g = new Access_token(1);
// echo $g->genNewToken();
// echo "<hr>";
// var_dump($g->isGenned());
// echo "<hr>";
// echo $g->getExpireDate();
// echo "<hr>";
// echo $g->getToken();
echo '<pre>';
// $email = 'admin@mail.com';
$token = 'MTcx.NDgxZGNlNTc0NjY0OGU2MWE0MGFlNDAwMDRlMjE4ZDQ1ZGNjNTExM2EyYzUxOTk2NGYzZGRkMTU3NjY3N2IxMzI2N2IwOWVmNmJkYWFiOTI';
// $res = __PDO__->pdo_query("select * from access_tokens as a , User where token = '$token' ",true);

$res = __PDO__->pdo_query("select * from access_tokens , User where token = '$token' and user_id = User.id",false);
// $res = __PDO__->pdo_select("access_tokens , User",array(
//     "token" => $token,
//     "user_id" => 'User.id'
// ),false);
// $res = __PDO__->pdo_query("select * from access_tokens as a , User where token = '$token' ",true);

// $res = Access_Token::loginWithToken($token);
// $res = User::LoginWithEmail('admin@mail.com','123');
// $res = User::loginWithToken($token);
print_r($res);
echo '</pre>';
// var_dump(empty($res));
}catch(PDOException $e){
    echo "website is down";
    die();
}


?>