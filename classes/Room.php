<?php
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
            static public function getAllRooms(){
                return __PDO__->pdo_select('Room');
            }
        }
?>