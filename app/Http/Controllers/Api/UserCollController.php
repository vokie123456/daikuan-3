<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Api\UserCollRepository;
use App\Repositories\Api\AppRepository;

class UserCollController extends Controller
{
    protected $user;
    protected $userCollRepository;
    
    public function __construct(UserCollRepository $userCollRepository)
    {
        $this->user = Auth::guard('api')->user();
        $this->userCollRepository = $userCollRepository;
    }

    public function toggleCollection($id, AppRepository $appRepository)
    {
        $app = $appRepository->getAppById($id);
        if(!$app) {
            $this->set_error('找不到APP');
        }else {
            $ret = $this->userCollRepository->toggleCollection($id, $this->user->id);
            if($ret['ret']) {
                $this->set_success($ret['msg'])->set_data('type', $ret['type']);
            }else {
                $this->set_error($ret['msg']);
            }
        }
        return response()->json($this->get_result());
    }

    public function getCollections()
    {
        $apps = $this->userCollRepository->getCollections($this->user->id);
        $this->set_success('获取成功')->set_data('apps', !empty($apps['data']) ? $apps : null);
        return response()->json($this->get_result());
    }
}
