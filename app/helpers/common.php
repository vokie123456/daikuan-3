<?php

/*
| -------------------------------------------------------------------------
| 常用工具函数
| -------------------------------------------------------------------------
| 
|
*/


if(!function_exists('in_array_i')) {
    /**
     * 匹配元素是否在数组中(不区分大小写)
     * 
     * @param  String  $search  搜索的字符
     * @param  Array   $array   被搜索的数组
     * @return Bool
     */
    function in_array_i($search, $array) {
        if(!empty($search) && is_array($array)) {
            return !empty(preg_grep('/' . preg_quote($search, '/') . '/i', $array));
        }
        return false;
    }
}

if(!function_exists('rm_path_prev_storage')) {
    /**
     * 去除storage路径前的字符
     * 
     * @param  String  $path  路径字符
     * @return String
     */
    function rm_path_prev_storage(String $path) {
        return preg_replace('/^[\/]?storage\//i', '', $path);
    }
}

if(!function_exists('check_mobile')) {
    //检查手机号码格式
    function check_mobile($tel) {
        return (bool)preg_match("/^1[34578]{1}\d{9}$/", $tel);
    }
}
