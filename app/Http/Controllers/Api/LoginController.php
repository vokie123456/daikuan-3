<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    
    public function login()
    {
        $username = request('telephone');
        $password = request('password');
        if(!$username) {
            $this->set_error('用户名不能为空!');
        }else if(!$password) {
            $this->set_error('密码不能为空!');
        }else if(
            Auth::attempt([
                'telephone' => $username,
                'password' => $password,
            ])
        ) {
            $user = Auth::user();
            if(isset($user['status']) && $user['status']) {
                $token = $user->createToken('DaiKuan')->accessToken;
                $this->set_success('登录成功')->set_data('token', $token);
            }else {
                $this->set_error('该帐号已被禁止!');
            }
        }else {
            $this->set_error('用户名或密码错误!');
        }
        return response()->json($this->get_result());
    }
}
