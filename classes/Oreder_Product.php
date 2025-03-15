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
*/

    class Order_Product{
        private $id;
        private $order_id;
        private $product_id;
        private $quantity;
        private $price_at_purchase;
      
        public function __construct($order_id, $product_id, $quantity){
            $this->order_id = $order_id;
            $this->product_id = $product_id;
            $this->quantity = $quantity;
            $this->Create();
        }
        private function Create(){
            if ($this->isCreated()){
                return false;
            }
            try{
                if (!Order::isOrderFoundInDB($this->order_id)){
                    return false;
                }
                if (!Product::isProductFoundInDB($this->product_id)){
                    return false;
                }
                if (!is_numeric($this->quantity) || !is_int($this->quantity) || $this->quantity < 0){
                    return false;
                }
                $product = Product::getProductById($this->product_id);
                $this->price_at_purchase = $product['price'];
                if (!is_numeric($this->price_at_purchase) || $this->price_at_purchase < 0){
                    return false;
                }
                $itemData = array();
                if(static::isItemFoundByOrderIdAndProductId($this->order_id, $this->product_id,$itemData)){
                    if (!$itemData || count($itemData) < 1){
                        return false;
                    }
                    $updateQuantity = __PDO__->pdo_update('order_product',array(
                        "quantity"=> $this->quantity + $itemData["quantity"],
                    ),array(
                        "order_id"=>$this->order_id,
                        "product_id"=>$this->product_id
                    ));
                    if ($updateQuantity>0){
                        $this->id = $itemData['id'];
                        return true;
                    }else{
                        return false;
                    }
                }
                $item_id = __PDO__->pdo_insert('order_product', get_object_vars($this));
                if ($item_id && $item_id > 0){
                    $this->id = $item_id;
                }else{
                    return false;
                }
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
    public static function getItemsByOrderId($order_id){
        if (!is_int( $order_id )){
            return [];
        }
        try{
            $items = __PDO__->pdo_select('Order_Product',array(
                'order_id'=>$order_id
            ));
            if ($items && count($items) > 0){
                return $items;
            }else{
                return [];
            }
        }catch(PDOException $e){
            return [];
        }
    }
    private static function isItemFoundByOrderIdAndProductId($order_id, $product_id,&$itemData){
        try{
            $item = __PDO__->pdo_select('Order_Product',array(
                "order_id"=>$order_id,
                "product_id"=>$product_id
            ),false);
            if ($item && count($item) > 0 && Logic_Function::isFound($item['id']) && Logic_Function::isFound($item['quantity'])){
                // print_r($item);
                $itemData = $item;
                return true;
            }else{
                return false;
            }
        }catch(PDOException $e){
            return false;
        }
    }
};
?>