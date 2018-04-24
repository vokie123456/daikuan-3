<?php 
/*
 |--------------------------------------------------------------------------
 | 短信类库
 |--------------------------------------------------------------------------
 | 
 |
 */

namespace App\Services;

//查询参数
class SmsCode {
    protected $_config = [
        'url' => 'http://api.1cloudsp.com/api/v2/send',
        'accesskey' => 'FuM1v4Fw3Z4Gs0dV',
        'secret' => '4LqKyfcNnEF3fdKoQD3kubjkQ36KWYwM',
        'sign' => '【贷款超市】',
        'templateId' => 3318,
        'scheduleSendTime'=>''
    ];

    //构造函数
    public function __construct(Array $config = []) {
        if(!empty($config)) {
            $this->_config = array_merge($this->_config, $config);
        }
    }

    public function send_code($phone, $code) {
        $datas = [
            'accesskey' => $this->_config['accesskey'],
            'secret' => $this->_config['secret'],
            'sign' => $this->_config['sign'],
            'templateId' => $this->_config['templateId'],
            'mobile' => $phone,
            'content' => $code,
            'scheduleSendTime'=>''
        ];
        return $this->_curl_setopt($datas);
    }

    protected function _curl_setopt($datas){
        $requestString = http_build_query($datas);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $this->_config['url']);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestString);
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headerArr = array();
        foreach($headers as $n => $v ) {
            $headerArr[] = $n .': ' . $v;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER , $headerArr);
    
        //request...
        $response = curl_exec($curl);
        if($response === false){
            if(curl_errno($curl) == CURLE_OPERATION_TIMEDOUT){
                error_log("短信接口请求超时！",0);
            }
            return false;
        }
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        curl_close($curl);
    
        $result = json_decode($body,true);
        if (!empty($body) && !is_null($result)){
            if($result['code'] == '0'){
                return true;//发送成功
            }else{
                error_log("短信发送失败code:" . $result['code'] . "，msg:" . $result['msg'], 0);
                return false;//发送失败
            }
        }else{
            error_log("短信接口返回内容为空或不是json格式！", 0);
            return false;
        }
    }
}