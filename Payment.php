<?php
/*
mysql> describe Payment;
+----------+--------------------------------------+------+-----+-------------------+-------------------+
| Field    | Type                                 | Null | Key | Default           | Extra             |
+----------+--------------------------------------+------+-----+-------------------+-------------------+
| id       | int                                  | NO   | PRI | NULL              | auto_increment    |
| date     | datetime                             | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| status   | enum('pending','completed','failed') | NO   |     | pending           |                   |
| method   | enum('cash','delivery','online')     | NO   |     | NULL              |                   |
| order_id | int                                  | NO   | MUL | NULL              |                   |
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
    }

    public function save($User_id) {
        if(User::getBalanceById($User_id) > 0){
            $data = [
                'order_id' => $this->order_id,
                'method' => $this->method,
                'status' => $this->status
            ];
            $this->id = __PDO__->pdo_insert('Payment', $data);
            return $this->id;
        } 
        else {
            return 'Insufficient balance';
        }

    }

    public function update() {
        if(User::getBalanceById($User_id) > 0){
            $data = [
                'status' => $this->status,
                'method' => $this->method
            ];
            return __PDO__->pdo_update('Payment', $data, ['id' => $this->id]);
        }
        else {
            return 'Insufficient balance';
        }

    }

    public function delete() {
        return __PDO__->pdo_delete('Payment', ['id' => $this->id]);
    }

    public static function findById($id) {
        $result = __PDO__->pdo_select('Payment', ['id' => $id]);
        return !empty($result) ? self::createFromArray($result[0]) : 'null';
    }

    public static function findByOrder($order_id) {
        $results = __PDO__->pdo_select('Payment', ['order_id' => $order_id]);
        return array_map([self::class, 'createFromArray'], $results);
    }

    private static function createFromArray($data) {
        $payment = new self(
            $data['order_id'],
            $data['method'],
            $data['status']
        );
        $payment->id = $data['id'];
        $payment->date = $data['date'];
        return $payment;
    }

    public function getId() { return $this->id; }
    public function getOrderId() { return $this->order_id; }
    public function getMethod() { return $this->method; }
    public function getStatus() { return $this->status; }
    public function getDate() { return $this->date; }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }
};
$test = new Payment(1, 'cash');

?>