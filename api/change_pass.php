<?php
User::fullTokenLoginCheck( $respone,$status, $message );
if ($status === "success"){
    User::ChangeUserPass( $respone, $status, $message );
}

$respone['status'] = $status;
$respone['message'] = $message;
?>
