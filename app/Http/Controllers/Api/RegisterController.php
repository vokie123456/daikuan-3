<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\SmsCodeRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    protected $smscode;
    
    public function __construct(SmsCodeRepository $smscode)
    {
        $this->smscode = $smscode;
    }

    public function sendCode(Request $request)
    {
        $error = $this->smscode->send_sms_code($request->get('telephone'), 0, false, [
            'templateId' => 4300,
            'add_expiry_time' => true,
        ]);
        if($error) {
            $this->set_error($error);
        }else {
            $this->set_success('发送成功')->set_data('code', $this->smscode->code);
        }
        return response()->json($this->get_result());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  Array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(Array $data)
    {
        return Validator::make($data, [
            'telephone' => 'required|string',
            'code' => 'required',
            'password' => 'required|string|min:6',
        ], [
            'telephone.required' => '手机号码不能为空',
            'code.required' => '验证码不能为空',
            'password.required' => '密码不能为空',
            'password.string' => '密码必须为字符串',
            'password.min' => '密码最少需要:min位',
        ]);
    }

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());
        if($validator->fails()) {
            $this->set_error($validator->errors()->first());
        }else {
            $telephone = $request->get('telephone');
            $code = $request->get('code');
            $password = $request->get('password');
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
                        $this->smscode->setUsed($telephone, $code, 0);
                    }else {
                        $this->set_error('用户添加失败!');
                    }
                }
            }
        }
        return response()->json($this->get_result());
    }


    // 重置密码

    public function sendFindPasswordCode(Request $request)
    {
        $error = $this->smscode->send_sms_code($request->get('telephone'), 1, true, ['templateId' => 4334]);
        if($error) {
            $this->set_error($error);
        }else {
            $this->set_success('发送成功')->set_data('code', $this->smscode->code);
        }
        return response()->json($this->get_result());
    }

    public function findPassword(Request $request, UserRepository $userRepository)
    {
        $validator = $this->validator($request->all());
        if($validator->fails()) {
            $this->set_error($validator->errors()->first());
        }else {
            $telephone = $request->get('telephone');
            $code = $request->get('code');
            $password = $request->get('password');
            $error = $this->smscode->checkCodeByPhone($telephone, $code, 1);
            if($error) {
                $this->set_error($error);
            }else {
                if(!$userRepository->getUserByPhone($telephone)) {
                    $this->set_error('该手机号码不存在!');
                }else {
                    $data = [
                        'password' => bcrypt($password),
                    ];
                    if($userRepository->update_info_by_telephone($telephone, $data)) {
                        $this->set_success('密码重置成功!');
                        $this->smscode->setUsed($telephone, $code, 1);
                    }else {
                        $this->set_error('密码重置失败!');
                    }
                }
            }
        }
        return response()->json($this->get_result());
    }
}
