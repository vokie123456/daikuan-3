<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Api\UserRecRepository;
use App\Repositories\Api\AppRepository;

class UserRecController extends Controller
{
    protected $user;
    protected $userRecRepository;
    
    public function __construct(UserRecRepository $userRecRepository) {
        $this->user = Auth::guard('api')->user();
        $this->userRecRepository = $userRecRepository;
    }

    public function getRecords()
    {
        $apps = $this->userRecRepository->getRecords($this->user->id);
        $this->set_success('获取成功')->set_data('apps', $apps);
        return response()->json($this->get_result());
    }
}
