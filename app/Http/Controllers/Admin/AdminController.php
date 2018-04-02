<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //获取管理者信息
    public function getInfo()
    {
        $admin = Auth::user();
        if($admin) {
            $this->set_success('获取成功')->set_data('admin', $admin->toArray());
        }else {
            $this->set_error('获取失败');
        }
        return response()->json($this->get_result());
    }
}
