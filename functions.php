<?php
    final class Logic_Function{
        static public $supported_img_types = array("image/png","image/jpeg", "image/jpg") ;
        static public $uploads_dir = "./uploads";
        static public function getMessages($errNum = 0){
            $errMsg = "test";
            $type = false;
            switch($errNum){
                case 1:
                $errMsg = "Error: You shouldn't forget any fields Empty !";
                $type = false;
                break;
                case 2:
                $errMsg = "Error: Passwords Are NOT Matched.";
                $type = false;
                break;
                case 3:
                $errMsg = "Error: Bad Email , please write your email correctly.";
                $type = false;
                break;
                case 4:
                $errMsg = "Error: bad Name , Your name should contains only Text.";
                $type = false;
                break;
                case 5:
                $errMsg = "Error: Bad Password , Use at least 8 characters and a mix of letters (uppercase and lowercase), numbers, and symbols.";
                $type = false;
                break;
                case 6:
                $errMsg = "Error: Room Number Should be only Numbers !";
                $type = false;
                break;
                case 7:
                $errMsg = "Error: Ext Should be only Number !";
                $type = false;
                break;
                case 8:
                $errMsg = "Error: Failed To Upload The Img , Unknown Error.";
                $type = false;
                break;
                case 9:
                $errMsg = "Error: Your Image Size is too big , our max size is 10MB.";
                $type = false;
                break;
                case 10:
                $errMsg = "Error: This Email is already Existed , please use another Email.";
                $type = false;
                break;
                case 11:
                $errMsg = "Error: Your uploaded file isn't image or it's not supported img.";
                $type = false;
                break;
                case 12:
                $errMsg = "Success: Your Account is just created successfuly.";
                $type = true;
                break;
                case 13:
                $errMsg = "Success: You logged in successfuly.";
                $type = true;
                break;
                case 14:
                $errMsg = "Error: Email/Password is/are incorrect.";
                $type = false;
                break;
                case 15:
                $errMsg = "Error: internal error , couldn't register , contact with admin.";
                $type = false;
                break;
                case 16:
                $errMsg = "Success: profile image has been changed successfuly.";
                $type = true;
                break;
                default:
                $errMsg = "UNKNOWN_ERROR";
                $type = false;
            }
            return array($type , $errMsg);
        }
        
        static public function isValidName(&$name) {
            if(static::isFound($name)) {
            $pattern = '/^[A-Za-z\s]+$/';
            return preg_match($pattern, $name);
            } else {
                return false;
            }
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
        static public function isExpired($expiryDate) {
            $expiryDate = $expiry = new DateTime($expiryDate);
            $now = new DateTime();
            $left = $now->diff($expiryDate);
            if ($left->invert == 0) {
                return true;
            }else{
                false;
            }
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