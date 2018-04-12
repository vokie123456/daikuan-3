<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\CategoryRepository;

class AppListController extends Controller
{
    protected $category;
    
    public function __construct(CategoryRepository $category)
    {
        $this->category = $category;
    }

        
    //
    public function getDatas($id)
    {
        $datas = $this->category->getCategoryAppById($id);
        $this->set_success('获取成功')->set_data('datas', $datas);
        return response()->json($this->get_result());
    }
}
