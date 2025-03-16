<?php
/*
mysql> describe Payment;
+----------+--------------------------------------+------+-----+-------------------+-------------------+
| Field    | Type                                 | Null | Key | Default           | Extra             |
+----------+--------------------------------------+------+-----+-------------------+-------------------+
| id       | int                                  | NO   | PRI | NULL              | auto_increment    |
| order_id | int                                  | NO   | MUL | NULL              |                   |
| method   | enum('cash','delivery','online')     | NO   |     | NULL              |                   |
| status   | enum('pending','completed','failed') | NO   |     | pending           |                   |
| date     | datetime                             | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+----------+--------------------------------------+------+-----+-------------------+-------------------+
5 rows in set (0.00 sec)

mysql> 

*/

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once('database.php');
require_once('User.php');
class Payment {
    private $id;
    private $order_id;
    private $method;
    private $status;
    private $date;

    public function __construct($order_id, $method, $status = 'pending') {
        $this->order_id = $order_id;
        $this->method = $method;
        $this->status = $status;
        $this->Create();
    }

    private function Create() {
        if ($this->isCreated()){
            return false;
        }
        try{

            if(!Order::isOrderFoundInDB($this->order_id)) {
                return false;
            }
            if (Order::isOrderCompleted($this->order_id)) {
                return false;
            }
            if(!static::isValidMethod($this->method)) {
                return false; 
            }
            if (!static::isValidStatus($this->status)) {
                return false;
            }
            $PaymentId = __PDO__->pdo_insert('payment',get_object_vars($this));
            if ($PaymentId > 0){
                $this->id = $PaymentId;
            }
            $this->Pay();
            return true;

        }catch(PDOException $e){
            return false;
        }

        
    }
    public function isCreated(){
        if ($this->id && $this->id > 0){
            return true;
        }else{
            return false;
        }
    }
    public static function isValidMethod($method){
        $validMethods = ['cash', 'delivery', 'online'];
        if (in_array($method, $validMethods)){
            return true;
        }else{
            return false;
        }
    }
    private static function isValidStatus($status){
        $validStatus = ['pending', 'completed','failed'];
        if (in_array($status, $validStatus)){
            return true;
        }else{
            return false;
        }
    }
    private function Pay(){
        try{
            $orderData = Order::getOrderDataById($this->order_id);
            // $totalPrice = Order::getOrderTotalPrice($this->order_id);
            $totalPrice = floatval($orderData['total_price']);
            if (Logic_Function::isFound($orderData['user_id']) && Logic_Function::isFound($totalPrice) && $totalPrice > 0){
                $userId = $orderData['user_id'];
                switch ($this->method){
                    case 'cash':
                        $Transaction = new Wallet_Transaction($userId,"sub" ,$totalPrice);
                        if ($Transaction->isCreated() && $Transaction->isCompleted()){
                            $this->updateStatus('completed');
                            Order::setStatus($this->order_id, 'completed');
                            return true;
                        }else{
                            $this->updateStatus('failed');
                            return false;
                        }
                        break;
                    case 'delivery':
                        $user_balance = User::getBalanceById($userId);
                        if ($user_balance >= $totalPrice){
                            $this->updateStatus('failed');
                            return false;
                        }else{
                            $this->updateStatus('completed');//payment completed but the admin has to confirm manually.
                            return true;
                        }
                        break;
                    case 'online':
                        $this->updateStatus('failed');
                        return false;
                        break;
                }





            }
        }catch(PDOException $e){
            return false;
        }
    }
    private function updateStatus($status){
        if ($this->isValidStatus($status) && $this->isCreated()) {
            $this->status = $status;
            $update = __PDO__->pdo_update('payment',array(
                'status'=> $this->status,
            ),array(
                'id'=> $this->id
            ));
            if ($update > 0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public static function isPaymentFoundInDB($id){
        //return false/true
        if (!is_numeric($id)){
            return false;
        }
        $order = __PDO__->pdo_select('payment',array(
            "id" => $id
        ),false);
        if ($order && count($order) > 0){//dont forget to add $order && in other classes , bug fixed.
            return true;
        }else{
            return false;
        }
    }
    public static function getPaymentById($id){
        try{
     if (static::isPaymentFoundInDB($id)){
        $Product = __PDO__->pdo_select('payment',array(
            'id'=> $id
        ),false);
        if ($Product && count($Product) > 0){
            return $Product;
        }else{
            return false;
        }
     }else{
        return false;
     } 
    }catch(PDOException $e){
        return false;
    }
    }

    public static function getAllPaymentsByOrderId($order_id){
        try{
     if (Order::isOrderFoundInDB($order_id)){
        $Product = __PDO__->pdo_select('payment',array(
            'order_id'=> $order_id
        ),true);
        if ($Product && count($Product) > 0){
            return $Product;
        }else{
            return [];
        }
     }else{
        return [];
     } 
    }catch(PDOException $e){
        return [];
    }
    }

    public static function getAllPayments(){
        try{
        $Product = __PDO__->pdo_select('payment');
        if ($Product && count($Product) > 0){
            return $Product;
        }else{
            return [];
        }

    }catch(PDOException $e){
        return [];
    }
    }


    public function getId() { return $this->id; }
    public function getOrderId() { return $this->order_id; }
    public function getMethod() { return $this->method; }
    public function getStatus() { return $this->status; }
    public function getDate() { return $this->date; }



    public function setMethod($method) {
        if ($this->isValidMethod($method)){
            $this->method = $method;
            return true;
        }else{
            return false;
        }
    }
    public static function isPaymentCompleted($payment_id){
        if (static::isPaymentFoundInDB($payment_id)){
            $payment = static::getPaymentById($payment_id);
            if ($payment){
                $isCompleted = $payment['status'];
                if ($isCompleted == 'completed'){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function isCompleted(){
        if ($this->isCreated()){
                $isCompleted = $this->getStatus();
                if ($isCompleted == 'completed'){
                    return true;
                }else{
                    return false;
                }
           
        }else{
            return false;
        }
    }


};

?>