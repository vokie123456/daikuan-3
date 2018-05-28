<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\Api\UserRecRepository;
use App\Repositories\Api\PromoteRepository;
use App\Services\Echarts;

class HomeController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.home');
    }

    public function getCounts(
        UserRepository $userRepository, 
        UserRecRepository $userRecRepository, 
        PromoteRepository $promoteRepository,
        Echarts $echarts
    ) {
        $new_users = $userRepository->getCountByToday();
        $datas['today'] = [
            'app_visit' => $userRecRepository->getCountByToday(),
            'promote' => $promoteRepository->getCountByToday(),
            'register' => $new_users['register'],
            'activate' => $new_users['activate'],
        ];

        $legend = ['APP浏览量', '推广量', '注册量', '激活量'];
        $start_date = $echarts->setDayCount(15)->getBeforeDate();
        $result = $echarts->setData($userRecRepository->getCountByDate($start_date))
                        ->setData($promoteRepository->getCountByDate($start_date))
                        ->setData($userRepository->getCountByDate($start_date))
                        ->setData($userRepository->getCountByDate($start_date, 'activated_at'), 'activated_at')
                        ->setSeriesData($legend)->getData();
        $datas['datas'] = [
            'legend' => $legend,
            'xaxis' => $echarts->getDays(),
            'series' => $result,
        ];
        $this->set_success('获取成功')->set_data('datas', $datas);
        return response()->json($this->get_result());
    }
}
