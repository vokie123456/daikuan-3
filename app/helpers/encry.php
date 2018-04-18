<?php

/*
| -------------------------------------------------------------------------
| 加密工具函数
| -------------------------------------------------------------------------
| 
|
*/


if(!function_exists('urlsafe_b64encode')) {
    //把base64_encode加密过的字符串转成安全的url字符串
    function urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }
}

if(!function_exists('urlsafe_b64decode')) {
    //解密urlsafe_b64encode加密过的字符串
    function urlsafe_b64decode($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if($mod4) $data .= substr('====', $mod4);
        return base64_decode($data);
    }
}

define('ENCODE_NUMBER', 900000);

if(!function_exists('create_url_encode_by_id')) {
    function create_url_encode_by_id($key, $id) {
        $string = json_encode([$key => dechex(ENCODE_NUMBER + intval($id))]);
        return urlsafe_b64encode($string);
    }
}

if(!function_exists('get_id_by_url_encode')) {
    function get_id_by_url_encode($string) {
        $ret = null;
        $json = urlsafe_b64decode($string);
        if($json) {
            $arr = json_decode($json, true);
            if($arr && count($arr)) {
                $key = key($arr);
                $ret = [
                    'key' => $key,
                    'val' => hexdec($arr[$key]) - ENCODE_NUMBER,
                ];
            }
        }
        return $ret;
    }
}
