<?php
    class ClientRequest{
        private function __construct(){}
        public static function getRequestAuth(){
            if (Logic_Function::isFound($_SERVER["HTTP_AUTHORIZATION"])) {
                $token = $_SERVER["HTTP_AUTHORIZATION"];
                $token = str_replace("Bearer ","",$token);
                if (Access_Token::isValidTokenSyntax( $token )) {
                    return $token;
                }else{
                    return false;
                }
            } else {
                return false;
            }
        }
        public static function getRequestMethod(){
            return $_SERVER["REQUEST_METHOD"] ?? "GET";
        }
        public static function getRequestData(){
            $method = static::getRequestMethod();
            switch ($method) {
                case "GET":
                return $_GET;
                break;
                default:
                return file_get_contents("php://input");
            }
        }
        public static function getRequestURI(){
            return explode("/", trim($_SERVER['REQUEST_URI'], "/"));
        }
        public static function getRequestEndPoint(){
            $RequestURI = static::getRequestURI();
            if (Logic_Function::isFound($RequestURI[0]) && $RequestURI[0] == 'api') {
                if (Logic_Function::isFound($RequestURI[1]) && $RequestURI[1] == 'admin') {
                    if (Logic_Function::isFound($RequestURI[2])) {
                        return 'admin/'.$RequestURI[2];
                    }
                }else{
                    return $RequestURI[1];
                }
            }else{
                return null;
            }
        }
        public static function isPostRequest(){
            if (static::getRequestMethod() == 'POST') {
                return true;
            }else{
                return false;
            }
        }
        public static function isGetRequest(){
            if (static::getRequestMethod() == 'GET') {
                return true;
            }else{
                return false;
            }
        }
    }
?>