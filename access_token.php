<?php
    class Access_Token{
        private $id;
        private $user_id;
        private $token;
        private $expiry;
        private function random_gen($user_id){
            return str_replace("=","",base64_encode($user_id).".".base64_encode(bin2hex(random_bytes(40))));
        }
        static public function isValidTokenSyntax(&$token){

            $token = str_replace("Bearer ","",$token);
            $token_explode = explode(".", $token);
            if (count($token_explode) == 2){
                if (is_numeric(base64_decode($token_explode[0]))){
                    if (strlen($token_explode[1]) == 107){
                        if (ctype_xdigit(base64_decode($token_explode[1]))){
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
            }else{
                return false;
            }
        }
        private function getNewExpireDate(){
            return date("Y-m-d H:i:s", strtotime("+1 day")); // Example: 1-hour expiry
        }
        public function __construct($user_id){
            $this->user_id = $user_id;
        }
        public function genNewToken(){
            try{
                
                $this->token = $this->random_gen($this->user_id);
                $this->expiry = $this->getNewExpireDate();
              
                $token_id = __PDO__->pdo_insert('access_tokens',get_object_vars($this));
                if ($token_id > 0){
                    $this->id = $token_id;
                    return $this->token;
                }else{
                    return false;
                }
            }catch(PDOException $e){
                return false;
            }
        }
        public function getToken(){
            return $this->token;
        }
        public function getExpireDate(){
            return $this->expiry;
        }
        public function isGenned(){
            if (!isset($this->id) || $this->id == null){
                return false;
            }else{
                return true;
            }
        }


    }