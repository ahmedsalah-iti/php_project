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
    }
?>