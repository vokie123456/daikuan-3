<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\MoudleRepository;

class HomeController extends Controller
{
    protected $homeRepository;
    
    public function __construct(MoudleRepository $homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

        
    //
    public function getDatas()
    {
        $datas = [
            'banners' => $this->homeRepository->getBanner(),
            'icons' => $this->homeRepository->getCategory(1),
            'category_apps' => $this->homeRepository->getApps(0),
        ];
        $this->set_success('获取成功')->set_data('datas', $datas);
        return response()->json($this->get_result());
    }
}
