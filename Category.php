<?php
    class Category{
        private $id;
        private $name;
        public function __construct($name){
            $this->name = $name;
            //add to db
            $this->Create();
        }
        public function Create(){
            if (!Logic_Function::isValidName($this->name)){
                return false;
            }
            try{
            $category_id = __PDO__->pdo_insert('Category', get_object_vars($this));
            if ($category_id && $category_id > 0){
                $this->id = $category_id;
            }else{
                return false;
            }
        }catch(PDOException $e){
            return false;
        }
    }
    public static function isCategoryFoundInDB($id){
        //return false/true
        if (!is_numeric($id)){
            return false;
        }
        $category = __PDO__->pdo_select('Category',array(
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
    public static function getCategoryNameById($id){
        try{

        
     if (static::isCategoryFoundInDB($id)){
        $category = __PDO__->pdo_select('Category',array(
            'id'=> $id
        ),false);
        $category_name = $category['name'];
        if (Logic_Function::isFound($category_name)){
            return $category_name;
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
    public static function getAllCategories(){
        try{
            $categories = __PDO__->pdo_select('Category');
            if (count($categories) > 0){
                return $categories;
            }else{
                return [];
            }
        }catch(PDOException $e){
            return [];
        }
    }
};
?>