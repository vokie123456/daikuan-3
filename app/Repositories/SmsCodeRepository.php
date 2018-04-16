<?php
namespace App\Repositories;

use App\Models\Sms_code;
use App\Services\SmsCode;

class SmsCodeRepository
{
    protected $sms_code;
    protected $_maxCount = 5;
    protected $expiry_time = 10;  //单位: 分钟
    public $code = '';
    
    public function __construct()
    {
        $this->sms_code = new Sms_code();
    }

    public function sendPrevCheck($phone, $checkCount = true)
    {
        $err = null;
        if(!$phone) {
            $err = '手机号码不能为空!';
        }else if(!check_mobile($phone)) {
            $err = '手机号码格式不正确!';
        }else if($checkCount && $this->sms_code->where('telephone', $phone)->count() >= $this->_maxCount) {
            $err = "同一个手机当天最多只能发送{$this->_maxCount}个!";
        }
        return $err;
    }

    public function send_sms_code($phone, $type = 0)
    {
        $error = $this->sendPrevCheck($phone);
        $types = [0, 1];
        if(!in_array($type, $types)) {
            $error = '类型不合法!';
        }else if(!$error) {
            $sms = new SmsCode();
            $this->code = rand(1000, 9999);
            $ret1 = $sms->send_code($phone, $this->code, $this->expiry_time);
            if($ret1) {
                $data = [
                    'code' => $this->code,
                    'telephone' => $phone,
                    'type' => $type,
                    'request_ip' => request()->getClientIp(),
                    'expires_at' => date('Y-m-d H:i:s', strtotime("+{$this->expiry_time} minutes")),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $ret2 = $this->sms_code::create($data);
                if(!$ret2) $error = '数据库添加失败!';
            }else {
                $error = '发送失败!';
            }
        }
        return $error;
    }

    public function checkCodeByPhone($phone, $code, $type = 0)
    {
        $err = $this->sendPrevCheck($phone, false);
        if(!$err) {
            $row = $this->sms_code->select('isUse', 'code', 'expires_at')
                ->where('telephone', $phone)->where('type', $type)
                ->orderBy('created_at', 'desc')
                ->first();
            if(!$row) {
                $err = '该手机未发送过验证码!';
            }else if($row->code != $code) {
                $err = '该验证码不正确!';
            }else if($row->isUse) {
                $err = '该验证码已被验证过了!';
            }else if($row->expires_at && strtotime($row->expires_at) < time()) {
                $err = '该验证码已过期!';
            }
        }
        return $err;
    }
}
