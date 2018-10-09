<?php

function curl_request($url, $data = null) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    
    if(!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    
    if(strpos($url, "https://") === 0) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }

    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    $output = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($output, true);
    return $result ? $result : $output;
}

function create_full_url($url = '', $default = '') {
    if(strpos($url, 'http') !== 0) {
        $http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
        $url = $http . $_SERVER['HTTP_HOST'] . (strpos($url, '/') === 0 ? $url : ('/' . $url));
    }
    return $url ? $url : $default;
}

return curl_request(create_full_url('api/getvsersion'));
