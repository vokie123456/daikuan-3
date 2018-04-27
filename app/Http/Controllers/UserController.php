<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Resources\UserListResource;

class UserController extends Controller
{
    protected $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    //
    public function index(Request $request)
    {
        $datas = UserListResource::collection($this->userRepository->getList($request->all()));
        $this->set_success('获取成功')->set_data('users', $datas);
        return response()->json($this->get_result());
    }

    public function getInfoById()
    {
        $id = request('id');
        $data = $this->userRepository->getInfoById($id);
        if($data)  $this->set_success('获取成功')->set_data('data', $data);
        else $this->set_error('获取失败');
        return response()->json($this->get_result());
    }

    public function updateStatus(Request $request)
    {
        $id = $request->get('id');
        $status = $request->get('status');
        if($id) {
            $status = (bool)$status;
            $ret = $this->userRepository->updateStatus($id, $status);
            if($ret) {
                $str = $status ? '成功开启' : '成功关闭';
                $this->set_success($str)->set_data('status', $status);
            }else $this->set_error('更新失败');
        }else {
            $this->set_error('缺少参数');
        }
        return response()->json($this->get_result());
    }

    public function resetPassword()
    {
        $id = request('id');
        if($id) {
            $password = config('my.site.reset_password');
            $data = ['password' => bcrypt($password)];
            $ret = $this->userRepository->update_info_by_id($id, $data);
            if($ret) {
                $str = '密码成功重置为: ' . $password;
                $this->set_success($str);
            }else $this->set_error('更新失败');
        }else {
            $this->set_error('缺少参数');
        }
        return response()->json($this->get_result());
    }
}
