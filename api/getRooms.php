<?php
    $res['data'] = Room::getAllRooms();
    if (Logic_Function::isFound($res)){
        $respone = $res;
        $status = 'success';
    }
    $respone['status'] = $status;
    $respone['message'] = $message;

?>