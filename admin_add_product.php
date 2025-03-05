<?php
        $product_test = new Product("product name",5.5,1);
        if ($product_test->isCreated()) {
            echo 'created';
        }else{
            echo 'not created';
        }
?>