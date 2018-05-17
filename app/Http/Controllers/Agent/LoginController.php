<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Repositories\AgentRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $agentRepository;
    
    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }

    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();
        if($user && $request['id']) {
            $agent = $this->agentRepository->getAgentById($request['id']);
            if($agent) {
                $request->session()->put('agent', $agent);
            }
        }
        if($request->session()->has('agent')) {
            return redirect('agents/myurl');
        }else {
            return view('agent/login');
        }
    }

    //
    public function form(Request $request)
    {
        $data = $request->all();
        $validator = $this->validator($data);
        $request->session()->flashInput(['username' => isset($data['username']) ? $data['username'] : '']);
        if($validator->fails()) {
            return view('agent/login', ['errors' => $validator->errors()]);
        }else {
            $agent = $this->agentRepository->getAgentByName($data['username']);
            if(!$agent) {
                return view('agent/login')->withErrors([
                    'username' => '该用户不存在!',
                ]);
            }else if(!Hash::check($data['password'], $agent->password)) {
                return view('agent/login')->withErrors([
                    'password' => '密码错误!',
                ]);
            }else {
                $request->session()->put('agent', $agent);
                return redirect('agents/myurl');
            }
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
    }

    public function logout()
    {
        session()->forget('agent');
        return redirect('agents/login');
    }
}
