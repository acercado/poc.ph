<?php

//for debuging
if ( ! function_exists('_debug')){
    function _debug($data_arr = array(), $exit = 0){
        if(is_array($data_arr) || is_object($data_arr)){
            echo '<pre>'; print_r($data_arr);echo '</pre>';
        }else{
            echo $data_arr;
        }
        if($exit == 1){
            exit();
        }
        return;
    }
}