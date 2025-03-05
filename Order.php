<?php
/*
mysql> describe `Order`;
+---------+----------------------------------------+------+-----+-------------------+-------------------+
| Field   | Type                                   | Null | Key | Default           | Extra             |
+---------+----------------------------------------+------+-----+-------------------+-------------------+
| id      | int                                    | NO   | PRI | NULL              | auto_increment    |
| status  | enum('pending','completed','canceled') | NO   |     | NULL              |                   |
| note    | varchar(255)                           | YES  |     | NULL              |                   |
| user_id | int                                    | YES  | MUL | NULL              |                   |
| room_id | int                                    | YES  | MUL | NULL              |                   |
| date    | datetime                               | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+---------+----------------------------------------+------+-----+-------------------+-------------------+
6 rows in set (0.00 sec)
*/
    class Order{
        private $id;
        private $status;
        private $note;
        private $user_id;
        private $room_id;
        private $date;

        public function __construct($user_id,$room_id,$note= null ,$status = 'pending'){
            
        }
    };
?>