<?php
/*
mysql> describe Wallet_Transaction;
+----------------+----------------------------+------+-----+-------------------+-------------------+
| Field          | Type                       | Null | Key | Default           | Extra             |
+----------------+----------------------------+------+-----+-------------------+-------------------+
| id             | int                        | NO   | PRI | NULL              | auto_increment    |
| user_id        | int                        | NO   | MUL | NULL              |                   |
| type           | enum('add','sub')          | NO   |     | NULL              |                   |
| amount         | decimal(10,2)              | NO   |     | NULL              |                   |
| balance_before | decimal(10,2)              | NO   |     | NULL              |                   |
| balance_after  | decimal(10,2)              | NO   |     | NULL              |                   |
| status         | enum('completed','failed') | NO   |     | failed            |                   |
| made_at        | datetime                   | NO   |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+----------------+----------------------------+------+-----+-------------------+-------------------+
8 rows in set (0.00 sec)
*/
class Wallet_Transaction{
    private $id;
    private $user_id;
    private $type;
    private $amount;
    private $balance_before;
    private $balance_after;
    private $status;
    private $made_at;

    public function __construct($user_id, $type,$amount){
        
    }
}
?>