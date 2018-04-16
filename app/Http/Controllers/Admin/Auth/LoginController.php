<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function postLogin(Request $request)
    {
        $data = $request->only('email', 'password');
        $result = Auth::guard('admin')->attempt($data, true);
        if ($result) {
            return redirect(route('admin'));
        } else {
            return redirect()->back()
                ->with('email', $request->get('email'))
                ->withErrors(['email' => '用户名或密码错误']);
        }
    }

    public function postLogout(Request $request)
    {
        Auth::guard('admin')->logout();
        return redirect(route('admin.login.show'));
    }
}
