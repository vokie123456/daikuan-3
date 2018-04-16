<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\SmsCodeRepository;
use App\Repositories\UserRepository;

class RegisterController extends Controller
{
    protected $smscode;
    
    public function __construct(SmsCodeRepository $smscode)
    {
        $this->smscode = $smscode;
    }

    public function sendCode(Request $request)
    {
        $error = $this->smscode->send_sms_code($request->get('telephone'), 0);
        if($error) {
            $this->set_error($error);
        }else {
            $this->set_success('发送成功')->set_data('code', $this->smscode->code);
        }
        return response()->json($this->get_result());
    }

    public function register(Request $request)
    {
        $telephone = $request->get('telephone');
        $code = $request->get('code');
        $password = $request->get('password');
        if($telephone && $code && $password) {
            if(strlen($password) < 6) {
                $this->set_error('密码长度过短!');
            }else {
                $error = $this->smscode->checkCodeByPhone($telephone, $code, 0);
                if($error) {
                    $this->set_error($error);
                }else {
                    $user = new UserRepository();
                    if($user->getUserByPhone($telephone)) {
                        $this->set_error('该手机已注册!');
                    }else {
                        $data = [
                            'telephone' => $telephone,
                            'password' => bcrypt($password),
                        ];
                        $ret = $user->create($data);
                        if($ret) {
                            $this->set_success('用户添加成功!');
                        }else {
                            $this->set_error('用户添加失败!');
                        }
                    }
                }
            }
        }else {
            $this->set_error('缺少参数!');
        }
        return response()->json($this->get_result());
    }
}
