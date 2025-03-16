<?php
    final class Logic_Function{
        static public $supported_img_types = array("image/png","image/jpeg", "image/jpg") ;
        static public $uploads_dir = "../uploads";
        
        static public function isValidName(&$name) {
            if(static::isFound($name)) {
            $pattern = '/^[A-Za-z\s]+$/';
            return preg_match($pattern, $name);
            } else {
                return false;
            }
        }
        static public function isValidId($id) {
            return Logic_Function::isFound($id) && filter_var($id, FILTER_VALIDATE_INT) !== false && (int)$id > 0;
        }
        static public function isValidPhone(&$phone) {
            if(static::isFound($phone)) {
                $pattern = '/^(010|011|012|015)\d{8}$/';
                return preg_match($pattern, $phone);
            } else {
                return false;
            }
        }
        
        static public function isValidPass(&$pass) {
            if(static::isFound($pass)) {
            $minLen = 8;
            if (strlen($pass) < $minLen) {
                return false;
            }
            if (!preg_match('/^(?=.*[\W])(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,50}$/', $pass)) {
                return false;
            }else{
                return true;
            }
        } else {
            return false;
        }
        }
        public static function randomStr($len = 50){
            return bin2hex(random_bytes($len));
        }
        public static function isValidImgSize($size,$maxSize = 10) {
            if($size / 1024 /1024 > $maxSize) {
                return false;
            }else{
                return true;
            }
        }

        static public function isValidImgType($type) {
            $type = strtolower($type);
            return in_array($type, static::$supported_img_types);
        }
        static public function isFound(&$k) {
            if (isset($k) && !empty($k)) {
                return true;
            }else{
                return false;
            }
        }
        static public function isValidEmailKey(&$k) {
            if (static::isFound($k)) {
                if (filter_var($k, FILTER_VALIDATE_EMAIL)) {
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
        static public function isValidJson($string) {
            json_decode($string);
            return json_last_error() === JSON_ERROR_NONE;
        }
        static public function isValidPrice($price) {
            if (static::isFound($price)) {
                if (is_numeric($price)) {
                    if ($price < 0) {
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
        static public function isExpired($expiryDate) {
            $expiryDate = new DateTime($expiryDate);
            $now = new DateTime();
        
            return $expiryDate < $now;
        }
        
        static public function uploadImg($imgFile,&$newImgPath,&$oldImgPath){
            $newImgPath = false;
            
            if(!is_dir(static::$uploads_dir)){
                if(!mkdir(static::$uploads_dir,0755,true)){
                    return false;
                }
            }
            if($imgFile["tmp_name"] && $imgFile['error'] == 0 && static::isValidImgType($imgFile["type"]) && static::isValidImgSize($imgFile['size'])){
                $imgPath = static::$uploads_dir."/".static::randomStr().str_replace("/",".",$imgFile["type"]);
                if(move_uploaded_file($imgFile["tmp_name"],$imgPath)){
                    if (static::isFound($oldImgPath) && $oldImgPath != "./uploads/empty.jpg"){
                        unlink(filename: $oldImgPath);
                    }
                    $newImgPath = $imgPath;
                    return true;
                }else{
                    return false;
                }
            }
        }

    }
?>