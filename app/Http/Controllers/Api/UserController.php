<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Http\Resources\UserResource;
use App\Repositories\SmsCodeRepository;

class UserController extends Controller
{
    //
    protected $user;
    protected $smscode;
    protected $userRepository;
    
    public function __construct(UserRepository $userRepository, SmsCodeRepository $smscode)
    {
        $this->smscode = $smscode;
        $this->userRepository = $userRepository;
        // Auth::user()  也行
        $this->user = Auth::guard('api')->user();
    }

    public function getInfo()
    {
        $user = new UserResource($this->user);
        if(isset($this->user['status']) && $this->user['status']) {
            $this->set_success('获取成功!')->set_data('user', $user);
        }else {
            $this->set_error('该帐号已被禁止!');
        }
        return response()->json($this->get_result());
    }

    public function validator($datas)
    {
        return Validator::make($datas, [
            'name' => 'required|string|min:2',
            'sex' => 'nullable|integer|in:1,2',
            'birthday' => 'nullable|string|date',
            'email' => 'nullable|email',
            'profession' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
    }

    public function update(Request $request)
    {
        $datas = $request->all();
        $validator = $this->validator($datas);
        if($validator->fails()) {
            $this->set_error($validator->errors()->first());
        }else {
            $data = [
                'name' => $datas['name'],
                'sex' => isset($datas['sex']) ? intval($datas['sex']) : 0,
                'birthday' => !empty($datas['birthday']) ? date('Y-m-d H:i:s', strtotime($datas['birthday'])) : null,
                'email' => !empty($datas['email']) ? $datas['email'] : '',
                'profession' => !empty($datas['profession']) ? $datas['profession'] : '',
                'address' => !empty($datas['address']) ? $datas['address'] : '',
            ];
            if($this->userRepository->update_info_by_id($this->user->id, $data)) {
                $this->set_success('用户资料修改成功!');
            }else {
                $this->set_error('用户资料修改失败!');
            }
        }
        return response()->json($this->get_result());
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed|different:old_password',
        ]);
        if($validator->fails()) {
            $this->set_error($validator->errors()->first());
        }else if(!$this->userRepository->check_password($this->user->id, $request->get('old_password'))) {
            $this->set_error('旧密码错误');
        }else {
            $data = [
                'password' => bcrypt($request->get('new_password'))
            ];
            if($this->userRepository->update_info_by_id($this->user->id, $data)) {
                $this->set_success('密码修改成功!');
            }else {
                $this->set_error('密码修改失败!');
            }
        }
        return response()->json($this->get_result());
    }

    public function update_telephone_send_code(Request $request)
    {
        $error = $this->smscode->send_sms_code($request->get('telephone'), 2, true, ['templateId' => 4335]);
        if($error) {
            $this->set_error($error);
        }else {
            $this->set_success('发送成功')->set_data('code', $this->smscode->code);
        }
        return response()->json($this->get_result());
    }

    public function update_telephone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telephone' => 'required|string',
            'code' => 'required',
        ], [
            'telephone.required' => '手机号码不能为空',
            'code.required' => '验证码不能为空',
        ]);
        if($validator->fails()) {
            $this->set_error($validator->errors()->first());
        }else {
            $telephone = $request->get('telephone');
            $code = $request->get('code');
            $error = $this->smscode->checkCodeByPhone($telephone, $code, 2);
            if($error) {
                $this->set_error($error);
            }else {
                if($this->userRepository->getUserByPhone($telephone)) {
                    $this->set_error('该手机号码已被注册!');
                }else {
                    $data = [
                        'telephone' => $telephone
                    ];
                    if($this->userRepository->update_info_by_id($this->user->id, $data)) {
                        $this->set_success('手机更换成功!');
                        $this->smscode->setUsed($telephone, $code, 2);
                    }else {
                        $this->set_error('手机更换失败!');
                    }
                }
            }
        }
        return response()->json($this->get_result());
    }
}
