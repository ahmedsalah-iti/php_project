<?php
    User::RegisterNewUser($respone, $status, $message);

    $respone['status'] = $status;
    $respone['message'] = $message;
?>