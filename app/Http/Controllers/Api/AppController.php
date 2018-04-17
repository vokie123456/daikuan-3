<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\AppRepository;
use App\Repositories\Api\CategoryRepository;

class AppController extends Controller
{        
    //
    public function getDatas(CategoryRepository $category, $id)
    {
        $datas = $category->getCategoryAppById($id);
        $this->set_success('获取成功')->set_data('datas', $datas);
        return response()->json($this->get_result());
    }

    public function getApp(AppRepository $appRepository, $id)
    {
        $app = $appRepository->getAppById($id);
        if(!$app) {
            $this->set_error('找不到APP');
        }else if(!$app['status']) {
            $this->set_error('该APP已下架');
        }else {
            $this->set_success('获取成功')->set_data('app', $app);
        }
        return response()->json($this->get_result());
    }
}
