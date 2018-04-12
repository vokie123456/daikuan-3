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
        ){
            $user = Auth::user();
            $token = $user->createToken('DaiKuan')->accessToken;
            $this->set_success('登录成功')->set_data('token', $token);
        }else {
            $this->set_error('用户名或密码错误!');
        }
        return response()->json($this->get_result());
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;

        return response()->json(['success'=>$success], $this->successStatus);
    }

    public function details()
    {
        // $user = Auth::user();
        $user = Auth::guard('api')->user();
        // var_dump($user);exit;
        return response()->json(['success' => $user]);
    }
}
