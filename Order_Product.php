<?php
/*
mysql> describe Order_Product;
+-------------------+---------------+------+-----+---------+----------------+
| Field             | Type          | Null | Key | Default | Extra          |
+-------------------+---------------+------+-----+---------+----------------+
| id                | int           | NO   | PRI | NULL    | auto_increment |
| order_id          | int           | NO   | MUL | NULL    |                |
| product_id        | int           | YES  | MUL | NULL    |                |
| quantity          | tinyint       | NO   |     | NULL    |                |
| price_at_purchase | decimal(10,2) | NO   |     | NULL    |                |
+-------------------+---------------+------+-----+---------+----------------+
5 rows in set (0.00 sec)
*/
    class Order_Product{
        private $id;
        private $order_id;
        private $product_id;
        private $quantity;
        private $price_at_purchase;
      
        public function __construct($order_id, $product_id, $quantity, $price_at_purchase){
            
        }
    };
?>