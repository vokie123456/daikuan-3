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
        'sign' => '',
        'templateId' => 3318,
        'scheduleSendTime' => ''
    ];

    //构造函数
    public function __construct(Array $config = []) {
        $this->_config['sign'] = config('my.site.sms_sign');
        if(!empty($config)) {
            $this->_config = array_merge($this->_config, $config);
        }
    }

    public function send_code($phone, $code) {
        $type = config('my.site.sms_type');
        if($type == 1) {
            return $this->__send_sms_code($phone, $code);
        }

        $datas = [
            'accesskey' => $this->_config['accesskey'],
            'secret' => $this->_config['secret'],
            'sign' => $this->_config['sign'],
            'templateId' => $this->_config['templateId'],
            'mobile' => $phone,
            'content' => $code,
            'scheduleSendTime' => ''
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

    //发送验证码短信
    public function __send_sms_code($phones, $code='') {
        $moudle = [
            4300 => '你的注册验证码: %s, %s分钟内有效, 打死不要告诉别人哦!',
            4334 => '你正在找回密码, 验证码: %s。',
            4335 => '你正在更换手机, 验证码: %s。',
        ];
        if(!isset($moudle[$this->_config['templateId']])) return false;
        $_CFG['yimei'] = array(
            'cdkey'=>'EUCP-EMY-SMS1-3XOBZ',
            'password'=>'C32EF2',
            'sign'=> $this->_config['sign'],
        );
        $content = $_CFG['yimei']['sign'] . call_user_func_array('sprintf', array_merge(
            [$moudle[$this->_config['templateId']]],
            explode('##', $code)
        ));
        $cdkey = $_CFG['yimei']['cdkey'];
        $password = $_CFG['yimei']['password'];
        if(is_array($phones)){
            $phone = implode(',',$phones);
        }else{
            $phone = $phones;
        }
        if(!empty($content)){
            $fileType = mb_detect_encoding($content,array('UTF-8','GBK','LATIN1','BIG5'));
            if($fileType != 'UTF-8'){
                $content = mb_convert_encoding($content ,"UTF-8" , $fileType);
            }
        }
        $content = urlencode(htmlspecialchars($content));
        $url = "http://hprpt2.eucp.b2m.cn:8080/sdkproxy/sendsms.action?cdkey={$cdkey}&password={$password}&phone={$phone}&message={$content}";
        //die($url);
        $str = file_get_contents($url);
        if(strpos($str,"<error>0</error>")){
            return true;
        }else{
            return false;
        }
    }
}