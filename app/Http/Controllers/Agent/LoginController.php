<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Agent;

class LoginController extends Controller
{
    //
    public function form(Request $request)
    {
        $data = $request->all();
        $validator = $this->validator($data);
        if($validator->fails()) {
            return view('agent/login', ['errors' => $validator->errors()]);
        }else {
            $agent = $this->checkLogin($data['username'], $data['password']);
            if(!$agent) {
                return view('agent/login')->withErrors([
                    'username' => '用户名或者密码错误.',
                ]);
            }
            $request->session()->put('agent', $agent);
            return redirect('agents/home');        
        }
    }

    public function checkLogin($name, $password)
    {
        return Agent::where('username', $name)->where('password', $password)->first();
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
    }
}
