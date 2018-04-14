<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\HomeRepository;
use App\Repositories\Api\SecLoanRepository;
use App\Repositories\Api\CategoryRepository;

class MoudleController extends Controller
{        
    //首页
    public function homeDatas(HomeRepository $homeRepository)
    {
        return $this->response($homeRepository->getDatas());
    }

    //贷款
    public function loanDatas(CategoryRepository $category)
    {
        return $this->response($category->getCategoryByType(2, false));
    }

    //秒放贷
    public function secloanDatas(SecLoanRepository $secLoanRepository)
    {
        return $this->response($secLoanRepository->getDatas());
    }

    //返回结果
    public function response($datas)
    {
        $this->set_success('获取成功')->set_data('datas', $datas);
        return response()->json($this->get_result());
    }
}
