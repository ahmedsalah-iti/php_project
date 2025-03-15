<?php
        $category_test = new Category('Test Category x');
        if ($category_test->isCreated()) {
            echo 'created';
        }else{
            echo 'not created';
        }
?>