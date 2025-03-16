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
            $this->user_id = $user_id;
            $this->room_id = $room_id;
            $this->note = $note;
            $this->status = $status;
            $this->date = date('Y-m-d H:i:s');//because we forgot to add NOTE NULL in date attribute in `Order` Table.
            $this->Create();
        }
        private function Create(){
            if ($this->isCreated()){
                return false;
            }
            try{
            if (!User::isUserIdFoundDB($this->user_id)){
                return false;
            }
            if (!Room::isRoomFoundById($this->room_id)){
                return false;
            }
            if (!static::isValidStatus($this->status)){
                return false;
            }
            $this->filterNote();
            $order_id = __PDO__->pdo_insert('`order`', get_object_vars($this));
            if ($order_id && $order_id > 0){
                $this->id = $order_id;
            }else{
                return false;
            }
        }catch(PDOException $e){
            return false;
        }
    }
    private function filterNote(){
        if (!empty($this->note)){
            $this->note = trim($this->note);
            $this->note = strip_tags($this->note);
            $this->note = htmlspecialchars($this->note, ENT_QUOTES, 'UTF-8');
            $this->note = str_replace(["\r\n", "\r", "\n"], ' ', $this->note);
            $this->note = addslashes($this->note);
        }
    }
    private static function isValidStatus($status){
        $validStatus = ['pending', 'completed', 'canceled'];
        if (in_array($status, $validStatus)){
            return true;
        }else{
            return false;
        }
    }
    public static function isOrderFoundInDB($id){
        //return false/true
        if (!is_numeric($id)){
            return false;
        }
        $order = __PDO__->pdo_select('`order`',array(
            "id" => $id
        ),false);
        if ($order && count($order) > 0){//dont forget to add $order && in other classes , bug fixed.
            return true;
        }else{
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
    public static function getOrderDataById($id){
        try{
     if (static::isOrderFoundInDB($id)){
        $order = __PDO__->pdo_select('`order`',array(
            'id'=> $id
        ),false);
        if (Logic_Function::isFound($order)){
            $orderItems = Order::getOrderItems(intval($id));
            $orderTotalPrice = Order::getOrderTotalPrice(intval($id));
            $order['total_price'] = floatval($orderTotalPrice);
            $order['items'] = $orderItems;
            return $order;
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
    public static function getAllOrders(){
        try{
            // $Products = __PDO__->pdo_select('Product');
            $orders = __PDO__->pdo_select('`order`');
            if (count($orders) > 0){
                return $orders;
            }else{
                return [];
            }
        }catch(PDOException $e){
            return [];
        }
    }
    public static function getOrderTotalPrice($id){
        try{
            if (!static::isOrderFoundInDB($id)){
                return 0.00;
            }
            $total_price = __PDO__->pdo_query("select getOrderTotalPrice($id) as totalPrice",false);
            if (!Logic_Function::isFound($total_price['totalPrice'])){
                return 0.00;
            }
            $totalPrice = $total_price['totalPrice'];
            if ($total_price > 0){
                return $totalPrice;
            }else{
                return 0.00;
            }
        }catch(PDOException $e){
            return 0.00;
        }
    }
    public static function setStatus($order_id,$status){
        try{
            if (!static::isOrderFoundInDB($order_id)){
                return false;
            }
            if (!static::isValidStatus($status)){
                return false;
            }
            $update_status = __PDO__->pdo_update('`order`',array(
                "status"=>$status
            ),array(
                "id"=>($order_id)
            ));
            if ($update_status > 0){
                return true;
            }else{
                return false;
            }
        }catch(PDOException $e){
            return false;
        }
    }
    public static function getOrderItems($order_id){
       return Order_Product::getItemsByOrderId($order_id);
    }
    public static function isUserHasAccessToOrder($order_id,$user_id){
        try{
            if (!static::isOrderFoundInDB($order_id)){
                return false;
            }
            if (!User::isUserIdFoundDB($user_id)){
                return false;
            }
            $hasAccess = __PDO__->pdo_select("`order`",array(
                "id" =>$order_id,
                "user_id"=>$user_id
            ),false);
            if (Logic_Function::isFound($hasAccess)){
                return true;
            }else{
                return false;
            }
        }catch(PDOException $e){
            return false;
        }
    }
    public static function getAllOrdersByUserId($user_id){
        try{
            $orders = __PDO__->pdo_select('`order`',array(
                'user_id'=>$user_id
            ));
            if ($orders && count($orders) > 0){
                //adding total price for each order , but removed duo to too much delay.
                // $updated_orders = array();
                // foreach ($orders as $order){
                    // if ($order['status'] == 'pending'){
                    // $orderTotalPrice = floatval(Order::getOrderTotalPrice($order['id']));
                    // $order['total_price'] = $orderTotalPrice;
                    // }
                    // $updated_orders[] = $order;
                // }
                // return $updated_orders;
                return $orders;
            }else{
                return [];
            }
        }catch(PDOException $e){
            return [];
        }
    }
    public function getId(){
        return $this->id;
    }
    

    public static function isOrderCompleted($order_id){
        if (static::isOrderFoundInDB($order_id)){
            $order = static::getOrderDataById($order_id);
            if ($order){
                $isCompleted = $order['status'];
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
};

?>