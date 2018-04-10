<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use App\Repositories\CategoryAppRepository;

class CategoryAppController extends Controller
{
    protected $category_app;
    
    public function __construct(CategoryAppRepository $category_app)
    {
        $this->category_app = $category_app;
    }

    /**
     * 分类下的app数据
     *
     * @return \Illuminate\Http\Response
     */
    public function index($category_id)
    {
        // DB::enableQueryLog();
        $datas = $this->category_app->getDatas($category_id);
        // error_log(print_r(DB::getQueryLog(), true));
        $this->set_success('获取成功')->set_data('datas', $datas);
        return response()->json($this->get_result());
    }

    public function migrate(Request $request)
    {
        $category_id = (int)$request->get('category_id');
        $selected = $request->get('selected');
        if($category_id && isset($selected)) {
            $this->category_app->setDatas($category_id, $selected);
            $this->set_success('设置成功');
        }else {
            $this->set_error('缺少参数');
        }
        return response()->json($this->get_result());
    }
}
