<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\AppRepository;
use App\Repositories\Api\CategoryRepository;
use App\Repositories\Api\UserRecRepository;
use App\Repositories\Api\UserCollRepository;
use App\Repositories\Api\PromoteRepository ;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{  
    protected $appRepository;
    
    public function __construct(AppRepository $appRepository)
    {
        $this->appRepository = $appRepository;
    }

    //
    public function getDatas($id, CategoryRepository $category)
    {
        $datas = $category->getCategoryAppById($id);
        $this->set_success('获取成功')->set_data('datas', $datas);
        return response()->json($this->get_result());
    }

    public function getApp($id, UserRecRepository $userRecRepository, UserCollRepository $userCollRepository)
    {
        $app = $this->appRepository->getAppById($id);
        if(!$app) {
            $this->set_error('找不到APP');
        }else if(!$app['status']) {
            $this->set_error('该APP已下架');
        }else {
            $user = Auth::guard('api')->user();
            $app['isCollection'] = false;
            $user_id = null;
            $app['share_url'] = config('my.site.register_path');
            if($user && $user->id) {
                $user_id = $user->id;
                $key = config('my.site.recomm');
                $app['share_url'] .= "?{$key}=" . create_url_encode_by_id('users', $user->id);
                $app['isCollection'] = (bool)$userCollRepository->checkUserIsCollection($app['id'], $user->id);
            }
            $userRecRepository->addRecord($app['id'], $user_id);
            $this->set_success('获取成功')->set_data('app', $app);
        }
        return response()->json($this->get_result());
    }

    public function getAppWebUrl($id, PromoteRepository $promoteRepository)
    {
        $user = Auth::guard('api')->user();
        if($user->status) {
            $app = $this->appRepository->getSimpleAppById($id);
            if(!$app) {
                $this->set_error('找不到APP');
            }else if(!$app['status']) {
                $this->set_error('该APP已下架');
            }else {
                $promoteRepository->click_promote($app->id, $user->id);
                $this->set_success('获取成功')->set_data('weburl', $app['weburl']);
            }
        }else {
            $this->set_error('该帐号已被禁止!');
        }
        return response()->json($this->get_result());
    }
}
