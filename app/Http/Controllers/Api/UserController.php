<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    //
    protected $user;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->user = $userRepository;
    }

    public function getInfo()
    {
        // $user = Auth::user();
        $user = Auth::guard('api')->user();
        if($user) {
            $this->set_success('获取成功!')->set_data('user', new UserResource($user));
        }else {
            $this->set_error('获取失败!');
        }
        return response()->json($this->get_result());
    }
}
