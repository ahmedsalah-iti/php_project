<?php
        // (
        //     [id] => 1
        //     [email] => admin@mail.com
        //     [first_name] => Admin
        //     [last_name] => User
        //     [password] => hashedpass1
        //     [phone] => 01234567891
        //     [profile_img] => 
        //     [role] => admin
        //     [room_id] => 1
        // )
        
        // (
        //     [id] => 2
        //     [name] => Conference Room
        // )
        class Room{
            static public function getRoomNameByRoomId($id){
                try{

                    $getRoomName = __PDO__->pdo_select('Room',array(
                        "id" => $id
                    ),false);
                    if ($getRoomName){
                        return $getRoomName['name'];
                    }
                }catch(PDOException $e){
                    return $id;
                }
            }
            static public function isRoomFoundById(&$id){
                try{
                    if (Logic_Function::isFound($id) && is_numeric($id)){
                        $getRoom = __PDO__->pdo_select('Room',array(
                            "id" => $id
                        ),false);
                        if ($getRoom['id'] == $id){
                            return true;
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                }   catch(PDOException $e){
                return false;
                }
            }
        }


        class User{
        public $id;
        public $email;
        public $first_name;
        public $last_name;
        public $password;
        public $phone;
        public $profile_img;
        public $role;
        public $room_id;
        public function __construct($first_name,$last_name,$email,$password,$phone,$room_id,$profile_img = null){
            $this->first_name = $first_name;
            $this->last_name = $last_name;
            $this->email = $email;
            $this->setHashedPassword($password);
            $this->phone = $phone;
            $this->room_id = $room_id;
            $this->role = 'customer';
            $this->profile_img = $profile_img;
            $userID = $this->RegisterToDB();
            if($userID)
            {
                $this->id = $userID;
            }
        }

        public function isRegistered(){
            if (!isset($this->id) || $this->id == null){
                return false;
            }else{
                return true;
            }
        }
        public function getId(){
            return $this->id;
        }
        public function getEmail(){
            return $this->email;
        }
        public function getName(){
            return $this->first_name." ".$this->last_name;
        }
        public function setPassword($oldPassword, $newPassword){
            if (password_verify($oldPassword, $this->password)){
                $this->password = password_hash($newPassword, PASSWORD_DEFAULT);
                //change it via DB;
                //if success -> true , else false;
                return true;
            }else{
                return false;
            }
        }
        public function getPhone(){
            return $this->phone;
        }
        public function getProfileImg(){
            return $this->profile_img;
        }
        public function setProfileImg($profile_img){
            $this->profile_img = $profile_img;
        }
        public function getRole(){
            return $this->role;
        }
        public function getUserRoomId(){
            return $this->room_id;
        }
        private function setHashedPassword($password){
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }
        private function RegisterToDB(){
            try{
                
                $userId = __PDO__->pdo_insert('User', (array)$this);
                if($userId > 0){
                    $this->id = $userId;
                    return true;
                }else{
                    return false;
                }
            }catch(PDOException $e){
                return false;
            }
        }
        static public function isEmailFoundDB($email){
            try{
                $isEmailFound = __PDO__->pdo_select('User',array(
                    "email" => $email
                ),false);
                if($isEmailFound){
                    return true;
                }else{
                    return false;
                }
            } catch(PDOException $e){
                return false;
            }
        }
        static public function isUserIdFoundDB($id){
            try{
                $isUserIdFound = __PDO__->pdo_select('User',array(
                    "id" => $id
                ),fetchAll: false);
                if($isUserIdFound){
                    return true;
                }else{
                    return false;
                }
            } catch(PDOException $e){
                return false;
            }
        }
        static public function isPhoneFoundDB($phone){
            try{
                $isPhoneFound = __PDO__->pdo_select('User',array(
                    "phone" => $phone
                ),false);
                if($isPhoneFound){
                    return true;
                }else{
                    return false;
                }
            } catch(PDOException $e){
                return false;
            }
        }
        static public function LoginWithEmail($email, $password){
            $email = strtolower(addslashes(htmlspecialchars(trim($email))));
            $password = addslashes(htmlspecialchars(trim($password)));
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                return [];
            }

            try{
                // $isEmailFound = static::isEmailFoundDB($email);
                $isEmailFound = __PDO__->pdo_select('User',array(
                    "email" => $email
                ),false);
                
                if($isEmailFound){
                    if (password_verify($password, $isEmailFound['password'])){
                        $getAccess = new Access_Token($isEmailFound['id']);
                        if ($getAccess->genNewToken()){
                            $token = $getAccess->getToken();
                            $getRoomName = Room::getRoomNameByRoomId($isEmailFound['room_id']);
                            $isEmailFound['room_name'] = $getRoomName;
                            $isEmailFound = array_merge($isEmailFound,["token" =>$token]);
                            unset($isEmailFound['password']);
                            if ($isEmailFound['profile_img'] == null){
                                $isEmailFound['profile_img'] = './uploads/empty.jpg';
                            }
                            return $isEmailFound;
                        }else{
                            return false;
                        }
                    }else{
                        return [];
                    }
                }else{
                    return [];
                }
            }catch(PDOException $e){
                return false;
            }
        }

        static public function loginWithToken($token){
            $token = str_replace("Bearer ","",$token);
            try{
                if (Access_Token::isValidTokenSyntax($token)){
                    // $res = __PDO__->pdo_select("access_tokens , User",array(
                    //     "token" => $token
                    // ),false);
                    $res = __PDO__->pdo_query("select * from access_tokens , User where token = '$token' and user_id = User.id",false);

                     if(!empty($res)){
                        if (Logic_Function::isExpired($res["expiry"])){
                            unset($res['password']);
                            $getRoomName = Room::getRoomNameByRoomId($res['room_id']);
                            $res['room_name'] = $getRoomName;
                            $res['id'] = $res['user_id'];
                            unset($res['user_id']);
                            if ($res['profile_img'] == null){
                                $res['profile_img'] = './uploads/empty.jpg';
                            }
                            // echo json_encode($res);
                            return $res;
                        }else{
                            return [];
                        }
                    }else{
                        return [];
                    }
                }else{
                    return [];
                }
            } catch(PDOException $e){
                return [];
            }

        }
        static public function isRealAdmin($token){
            try{
                $login = static::loginWithToken($token);
                if(Logic_Function::isFound($login)){
                    if (isset($login['token']) && $login['token'] == $token && $login['role'] == 'admin'){
                        return true;
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

        static public function fullTokenLoginCheck(&$respone,&$status,&$message){
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                if (Logic_Function::isFound($_SERVER["HTTP_AUTHORIZATION"])) {
                    $token = $_SERVER["HTTP_AUTHORIZATION"];
                    $login = User::loginWithToken($token);
                    if (Logic_Function::isFound($login)) {
                        $status = "success";
                        $respone["data"] = $login;
                        $message = "logged in successfuly.";
                        
                    } else {
                        $message = "wrong / expired token.";
                    }
                } else {
                    $message = "missing HTTP_AUTHORIZATION.";
                }
            } else {
                $status = "failed";
                $message = "BAD REQUEST METHOD";
            }

        }
        static public function uploadProfileImgByEmail($userEmail,&$oldImgPath){
            try{
                $newImgPath = false;
                if (Logic_Function::isFound($_FILES['profile_img'])) {
                    if (Logic_Function::uploadImg($_FILES['profile_img'],$newImgPath,$oldImgPath)){
                        if ($newImgPath){
                try{
                    $isEmailFound = static::isEmailFoundDB($userEmail);
                    if ($isEmailFound){
                        $update_img = __PDO__->pdo_update("User",array("profile_img" => $newImgPath),array("email"=> $userEmail));
                if ( $update_img> 0){
                    $oldImgPath = $newImgPath;
                    return true;
                }else{
                return false;
                }
            }else{
            return false;
            }
            }catch (Exception $e){
        return false;
            }
    
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                }

            }catch(PDOException $e){
                return [];
            }
        }

        static public function uploadProfileImgByUserId($userId,&$oldImgPath){
            try{
                $newImgPath = false;
                if (Logic_Function::isFound($_FILES['profile_img'])) {
                    if (Logic_Function::uploadImg($_FILES['profile_img'],$newImgPath,$oldImgPath)){
                        if ($newImgPath){
                try{
                    $isUserIdFound = static::isUserIdFoundDB($userId);
                    if ($isUserIdFound){
                        $update_img = __PDO__->pdo_update("User",array("profile_img" => $newImgPath),array("id"=> $userId));
                if ( $update_img> 0){
                    $oldImgPath = $newImgPath;
                    return true;
                }else{
                return false;
                }
            }else{
            return false;
            }
            }catch (Exception $e){
        return false;
            }
    
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                }

            }catch(PDOException $e){
                return [];
            }
        }


        static public function RegisterNewUser(&$respone,&$status,&$message){
            /*
            {
                "first_name": "ahmed",
                "last_name": "salah",
                "email": "as@as.cc",
                "password": "123",
                "phone": "123",
                "room_id": "2"
            }
            */
           $status = 'failed';
           $message = '';
            try{
                if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                    $rawPostData = file_get_contents("php://input");
                    if(Logic_Function::isValidJson($rawPostData)){
                        $json = json_decode($rawPostData, true);
                        if(Logic_Function::isValidName($json['first_name']) && Logic_Function::isValidName( $json['last_name'])){
                            if (Logic_Function::isValidEmailKey(k: $json['email'])){
                                if (Logic_Function::isValidPass( $json['password'] )){
                                    if (Logic_Function::isValidPhone( $json['phone'] )){
                                        if (Room::isRoomFoundById( $json['room_id'] )){
                                            
                                            if(!static::isEmailFoundDB( $json['email'] )){
                                                if (!static::isPhoneFoundDB( $json['phone'] )){
                                                    $newUser = new User($json['first_name'], $json['last_name'], $json['email'], $json['password'], $json['phone'] , $json['room_id']);
                                                    if($newUser->isRegistered()){
                                                        $newToken = new Access_Token($newUser->id);
                                                        if ($newToken->genNewToken()){
                                                            $respone['data'] = get_object_vars($newUser);
                                                            unset($respone['data']['password']);
                                                            $respone['data']['token'] = $newToken->getToken();
                                                            $respone['data']['room_name'] = Room::getRoomNameByRoomId( $json['room_id'] );
                                                            if ($respone['data']['profile_img'] == null){
                                                                $respone['data']['profile_img'] = './uploads/empty.jpg';
                                                            }
                                                            $status = 'success';
                                                            $message = 'Your Account is Registerred Successfully.';
                                                        }else{
                                                            $message = 'something went wrong while generating your token.';
                                                        }
                                                        
                                                    }else{
                                                        $message = 'something went wrong while registerring your account.';
                                                    }
                                                }else{
                                                    $message = "this phone number is already registered before in our system.";
                                                }
                                                
                                            }else{
                                                $message = "this email is already registered before in our system !.";
                                            }

                                        }else{
                                            $message = "wrong Room , this room isn't existed in our system yet !.";
                                        }
                                    }else{
                                        $message = 'bad phone format , phone should start with 010/011/012/015.';
                                    }
                                }else{
                                    $message = 'bad password , please use stronger pass.';
                                }
                            }else{
                                $message = 'invalid email format.';
                            }
                        }else{
                            $message = 'invalid name format.';
                        }
                        
                    }else{
                        $message = 'invalid request body format.';
                    }
                }else{
                    $message = 'BAD REQUEST METHOD';
                }









            }catch(PDOException $e){
                $status = 'failed';
                $message = 'unknown error.';
                return false;
            }
        }
        static public function ChangeUserPass(&$respone,&$status,&$message){
            $status = 'failed';
            $message = '';
            try{
                if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                    $rawPostData = file_get_contents("php://input");
                    if(Logic_Function::isValidJson($rawPostData)){
                        $json = json_decode($rawPostData, true);
                        if(Logic_Function::isValidPass($json["new_pass"])){
                            if(Logic_Function::isValidEmailKey($respone['data']['email'])){
                                $realOldPass = static::getPasswordByEmail($respone['data']['email']);
                                if ($realOldPass){
                                    if(password_verify($json['current_pass'], $realOldPass)){
                                        $newPass = password_hash($json['new_pass'], PASSWORD_DEFAULT);
                                        $updatePass = __PDO__->pdo_update('User',array(
                                            'password'=> $newPass
                                        ),array(
                                            'email' => $respone['data']['email']
                                        ));
                                        if($updatePass > 0){
                                            $message = 'password is updated successfully.';
                                            $status ='success';
                                        }else{
                                            $message = "couldn't update the password for unknown error.";
                                        }
                                    }else{
                                        $message = 'Current Password is not Correct !';
                                    }
                                }else{
                                    $message = 'unknown errors';
                                }
                            }else{
                                $message = 'unknown error';
                            }
                        }else{
                            $message = 'Bad Password , please choose stronger pass !.';
                        }
                    }else{
                        $message = 'invalid request body format.';
                    }
                }else{
                    $message = 'BAD REQUEST METHOD';
                }
            }catch(Exception $e){
                $status = 'failed';
                $message = 'unknown error.';
                return false;
            }
        }
        static private function getPasswordByEmail($email){
            $pass = false;
            try{
                if(Logic_Function::isValidEmailKey($email)){
                    $userData = __PDO__->pdo_select('User',array(
                        'email' => $email
                    ),false);
                    if(Logic_Function::isFound($userData['password'])){
                        $pass = $userData['password'];
                        return $pass;
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

        static public function getAllMembers(&$respone,&$status,&$message){
            $status = 'failed';
            $message = '';
            try{
                    $allUsers = __PDO__->pdo_select('User');
                    if (Logic_Function::isFound($allUsers)){
                        $respone['all_members_data'] = $allUsers;
                        $status = 'success';
                        $message = 'fetch all members is successfuly done.';
                    }else{
                        $status = 'failed';
                        $message = 'failed to get users , unknown error';
                        return false;
                    }
        
            }catch(Exception $e){
                $status = 'failed';
                $message = 'unknown error.';
                return false;
            }
        }
        static public function updateUserInfoById($id , $data , &$message = ""){
            /*
              {
                "id": 1,
                "first_name": "Admin",
                 "last_name": "User",
                 "phone": "01234567891",
                 "email": "admin@mail.com",
                 "role": "admin",
                "room_id": "1"
              }
           */
          try{


          if (Logic_Function::isFound($id) && is_numeric($id) && $id > 0 && static::isUserIdFoundDB($id)){
            $filtered_data = [];
            if (Logic_Function::isValidName($data['first_name'])){
                $filtered_data['first_name'] = $data['first_name'];
            }
            if (Logic_Function::isValidName($data['last_name'])){
                $filtered_data['last_name'] = $data['last_name'];
            }
            if (Logic_Function::isValidEmailKey($data['email'])){
                $filtered_data['email'] = $data['email'];
            }
            if (Logic_Function::isValidPhone($data['phone'])){
                $filtered_data['phone'] = $data['phone'];
            }
            if (Logic_Function::isFound($data['role']) && ($data['role'] === 'admin' || $data['role'] === 'customer')){
                $filtered_data['role'] = $data['role'];
            }
            if (Logic_Function::isFound($data['room_id']) && is_numeric($data['room_id']) && Room::isRoomFoundById($data['room_id'])){
                $filtered_data['room_id'] = $data['room_id'];
            }
            if (count($filtered_data) > 0){
                $update = __PDO__->pdo_update('User',$filtered_data,array(
                    'id'=> $id
                ));
                if ($update > 0){
                    $message = "The User's data has been updated successfuly.";
                    return true;
                }else{
                    $message = "unknown error while updating User's Data.";
                    return false;
                }
            }else{
                $message = "couldn't update anything ! , something went wrong or empty data !.";
                return false;
            }
          }else{
            $message = "Not Valid ID , or this Id not found in our System !.";
            return false;
          }
          
        }catch(PDOException $e){
            $message = "something went wrong in our system !";
            return false;
          }

        }
    }
?>