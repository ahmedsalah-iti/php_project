<?php
    final class DB_CFG{
        private $host;
        private $user;
        private $password;
        private $database_name;
        private $port;
        private $type;
        public function __construct($host,$dbName,$user,$pass,$port = 3306, $type = "mysql"){
            $this->host = $host;
            $this->user = $user;
            $this->password = $pass;
            $this->database_name = $dbName;
            $this->port = $port;
            $this->type = $type;
        }
        public function getDsn($charset = 'utf8mb4'){
            return "$this->type:host=$this->host;port=$this->port;dbname=$this->database_name;charset=$charset";
        }
        public function getUser(){
            return $this->user;
        }
        public function getPass(){
            return $this->password;
        }
        
    }
    class Database{
        private $pdo;
        private $cfg;
        public function __construct($cfg){
            try{
            $this->cfg = $cfg;
            $this->pdo = new PDO($this->cfg->getDsn(),$this->cfg->getUser(),$this->cfg->getPass());
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "connected";
            }catch(PDOException $e){
                die("error in db,contact with admin.");
            }
        }
        public function getConnection(){
            return $this->pdo;
        }
        private function isAssociative(array $arr): bool {
            return array_keys($arr) !== range(0, count($arr) - 1);
        }
        public function pdo_select($table, $condition = [],$fetchAll = true){
            $this->getConnection();
            $q = "";
            try{
    
            if ($table){
                // $table = "`$table`";
                $table = "$table";
                $q = "select * from $table";
                if ($condition && $this->isAssociative($condition)){
                    $q.=" where ";
                    $whereClauses = [];
                    foreach ($condition as $col => $v){
                        $whereClauses[] = " $col = :$col ";
                    }
                    $q.= implode(" AND ", $whereClauses);
    
                }   
                $stmt = $this->pdo->prepare($q);
                $stmt->execute($condition);
    
            return $fetchAll ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                return [];
            }
    
        }catch(PDOException $e){
            return [];
            // throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
        }
        public function pdo_insert($table, $columns_values){
            try{
            $this->getConnection();
            $placeholders = [];
            foreach ($columns_values as $col => $v){
                $placeholders[":$col"] = $v;
            }
            $columns = implode(", ", array_keys($columns_values));
            $placeholdersStr = implode(", ", array_keys($placeholders));
            $q = "insert into $table ($columns) VALUES($placeholdersStr)";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute($placeholders);
            return $this->pdo->lastInsertId();
        }catch(PDOException $e){
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
        }
        public function pdo_update($table, $columns_values, $conditions){
            try{
                $this->getConnection();
                if ($table){
                    if ($this->isAssociative($columns_values) && $this->isAssociative(($conditions))){
                        $q = "update $table SET ";
                        foreach ($columns_values as $col => $v){
                            $q .= "$col = :$col , ";
                        }
                        $q = rtrim($q,", ");
                        $q .= " where ";
                        foreach($conditions as $col => $v){
                            $q .= "$col = :x$col AND ";
                        }
                        $q = rtrim($q,"AND ");
                        // echo $q;
                        $stmt = $this->pdo->prepare($q);
                        $params = array_merge(
                            $columns_values,
                            array_combine(
                                array_map(
                                    function($col){return ":x$col";},array_keys($conditions)
                                ),
                                array_values($conditions)
                            )
                            );
                            // print_r($params);
                        $stmt->execute($params);
                        return $stmt->rowCount();
                        
                    }else{  
                        return -2;
                    }
                }else{
                    return -3;
                }
            } catch(PDOException $e){
                // throw new PDOException($e->getMessage(), (int)$e->getCode());
                return -1;
            }
    
        }
        public function pdo_query($query,$fetchAll = true){
            try{
                $this->getConnection();
                $stmt = $this->pdo->prepare($query);
                $stmt->execute();
                return $fetchAll ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
            
            }catch(PDOException $e){
                return [];
            }
        }

    }

    $db_cfg = new DB_CFG("localhost","cafeteria","php_tester","123");
    $db = new Database($db_cfg);
    define('__PDO__',$db);
    /*
    CREATE TABLE access_tokens (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expiry DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(id)
    );
    */
?>