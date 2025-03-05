<?php
    class Product{
        private $id;
        private $name;
        private $price;
        private $category_id;
        private $status;
        private $product_img;

        public function __construct($name, $price, $category_id, $status = True, $product_img = null){
            $this->name = $name;
            $this->price = $price;
            $this->category_id = $category_id;
            $this->status = $status;
            $this->product_img = $product_img;
            //create new product function is here 
            $this->Create();
        }
        private function Create(){
            if (!$this->isValidProduct()){
                return false;
            }
            try{
            $product_id = __PDO__->pdo_insert('Product',get_object_vars($this));
            if ($product_id && $product_id > 0){
                $this->id = $product_id;
                return $product_id;
            }else{
                return false;
            }
        }catch(PDOException $e){
            return false;
        }
    }
    private function isValidProduct(){
        if (!Logic_Function::isValidName($this->name)){
            return false;
        }
        if (!Logic_Function::isValidPrice($this->price)){
            return false;
        }
        if (!Category::isCategoryFoundInDB($this->category_id)){
            return false;
        }
        if ($this->status){
            $this->status = 1;
        }else{
            $this->status = 0;
        }

    }
    public static function isProductFoundInDB($id){
        //return false/true
        if (!is_numeric($id)){
            return false;
        }
        $category = __PDO__->pdo_select('Product',array(
            "id" => $id
        ),false);
        if (count($category) > 0){
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
    public static function getProductNameById($id){
        try{
     if (static::isProductFoundInDB($id)){
        $Product = __PDO__->pdo_select('Product',array(
            'id'=> $id
        ),false);
        $Product_name = $Product['name'];
        if (Logic_Function::isFound($Product_name)){
            return $Product_name;
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
    public static function getAllProducts(){
        try{
            // $Products = __PDO__->pdo_select('Product');
            $Products = __PDO__->pdo_query('select Product.*,Category.name as category_name from Product , Category where Product.category_id = Category.id');

            if (count($Products) > 0){
                return $Products;
            }else{
                return [];
            }
        }catch(PDOException $e){
            return [];
        }
    }
};
?>