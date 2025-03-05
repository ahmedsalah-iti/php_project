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
class Payment{
    private $id;
    private $order_id;
    private $method;
    private $status;
    private $date;

    public function __construct($order_id, $method, $status = 'pending'){
        
    }
};
?>