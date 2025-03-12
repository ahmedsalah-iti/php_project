<?php
/*
mysql> describe Wallet_Transaction;
+----------------+----------------------------+------+-----+-------------------+-------------------+
| Field          | Type                       | Null | Key | Default           | Extra             |
+----------------+----------------------------+------+-----+-------------------+-------------------+
| id             | int                        | NO   | PRI | NULL              | auto_increment    |
| user_id        | int                        | NO   | MUL | NULL              |                   |
| type           | enum('add','sub')          | NO   |     | NULL              |                   |
| amount         | decimal(10,2)              | NO   |     | NULL              |                   |
| balance_before | decimal(10,2)              | NO   |     | NULL              |                   |
| balance_after  | decimal(10,2)              | NO   |     | NULL              |                   |
| status         | enum('completed','failed') | NO   |     | failed            |                   |
| made_at        | datetime                   | NO   |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+----------------+----------------------------+------+-----+-------------------+-------------------+
8 rows in set (0.00 sec)
*/
class Wallet_Transaction{
    private $id;
    private $user_id;
    private $type;
    private $amount;
    private $balance_before;
    private $balance_after;
    private $status;
    private $made_at;

    public function __construct($user_id, $type,$amount){
        $this->user_id = $user_id;
        $this->type = $type;
        $this->amount = $amount;
        $this->Create();
    }
    private function Create(){
        try{
            if(!User::isUserIdFoundDB($this->user_id)){
                return false;
            }
            
            
            if ($this->type !== "sub" && $this->type !== "add"){
                return false;
            }
            if ($this->type === "add"){
                $walletTransaction = __PDO__->pdo_query("select addUserBalance($this->user_id,$this->amount) as tid",false);
            }elseif ($this->type === "sub"){
                $walletTransaction = __PDO__->pdo_query("select addUserBalance($this->user_id,$this->amount) as tid",false);
            }
            if ($walletTransaction && count($walletTransaction) > 0 && Logic_Function::isFound($walletTransaction["tid"]) && $walletTransaction["tid"] > 0){
                $tid= $walletTransaction["tid"];
                $TransactionDetails = static::getTransactionDataById($tid);
                if ($TransactionDetails){
                $this->id = $TransactionDetails["id"];
                $this->balance_before = $TransactionDetails["balance_before"];
                $this->balance_after = $TransactionDetails["balance_after"];
                $this->status = $TransactionDetails["status"];
                $this->made_at = $TransactionDetails["made_at"];
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
    public function isCreated(){
        if ($this->id && $this->id > 0){
            return true;
        }else{
            return false;
        }
    }
    public function getTransactionData(){
        if($this->isCreated()){
            return get_object_vars($this);
        }else{
            return [];
        }
    }
    public static function isTransactionFoundInDB($id){
        //return false/true
        if (!is_numeric($id)){
            return false;
        }
        $transaction = __PDO__->pdo_select('Wallet_Transaction',array(
            "id" => $id
        ),false);
        if ($transaction && count($transaction) > 0){//dont forget to add $order && in other classes , bug fixed.
            return true;
        }else{
            return false;
        }
    }
    public static function getTransactionDataById($tid){
        try{
        if(static::isTransactionFoundInDB($tid)){
                $transaction = __PDO__->pdo_select("wallet_transaction",array(
                    "id"=> $tid
                ),false);
                if ($transaction && count($transaction) > 0){
                    return $transaction;
                }else{
                    return [];
                }

        }else{
            return [];
        }
    }catch(PDOException $e){
        return [];
    }
    }
    public static function getAllTransactionsByUserId($user_id){
        
            try{
                $transactions = __PDO__->pdo_select("wallet_transaction",array(
                    "user_id"=> $user_id
                ));
                if ($transactions && count($transactions) > 0){
                    return $transactions;
                }else{
                    return [];
                }
            }catch(PDOException $e){
                return [];
            }
        }
        public static function getAllTransactions(){
        
            try{
                $transactions = __PDO__->pdo_select("wallet_transaction");
                if ($transactions && count($transactions) > 0){
                    return $transactions;
                }else{
                    return [];
                }
            }catch(PDOException $e){
                return [];
            }
        }
    public static function isCompletedTransaction($tid){
        try{
            if(static::isTransactionFoundInDB($tid)){
                $transaction = static::getTransactionDataById($tid);
                if ($transaction && count($transaction) > 0){
                    if ($transaction["status"] == "completed"){
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
        }catch(PDOException $e){
            return false;
        }
    }
    public function isCompleted(){
        try{
            if($this->isCreated()){
                $transaction = $this->getTransactionData();
                if ($transaction && count($transaction) > 0){
                    if ($transaction["status"] == "completed"){
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
        }catch(PDOException $e){
            return false;
        }
    }
    }
?>