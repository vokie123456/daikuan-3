<?php
namespace App\Repositories\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Api\CategoryRepository;
use App\Repositories\BannerRepository;
use App\Http\Resources\Api\BannerResource;
use App\Repositories\Api\AppRepository;

class HomeRepository
{
    protected $banner;
    protected $category;
    
    public function __construct(CategoryRepository $category, BannerRepository $banner)
    {
        $this->banner = $banner;
        $this->category = $category;
    }

    protected function getBanner()
    {
        $query = [
            'sort' => 'sort',
            'order' => 'asc',
            'search' => [
                'position' => array_search(config('my.site.moudle_type')[0], config('my.site.banner_position')),
                'show_time' => date('Y-m-d H:i:s'),
                'status' => 1,
            ],
        ];
        $data = $this->banner->getList($query);
        return BannerResource::collection($data['rows']);
    }

    public function getDatas()
    {
        $apps = $this->category->getCategoryByType(0);
        return [
            'banners' => $this->getBanner(),
            'icons' => $this->category->getCategoryByType(1, false),
            // 'category_apps' => $this->category->getCategoryByType(0),
            'recomm_apps' => isset($apps[0]) ? $apps[0] : null,
            'hot_apps' => isset($apps[1]) ? $apps[1] : null,
            'pushers' => $this->getRandData(),
        ];
    }

    public function getRandData()
    {
        $timer = 2;
        $max = 500;
        $key = 'pushers';
        $empty = true;
        $probability = 4;

        $datas = Cache::has($key) ? Cache::get($key) : null;
        $now = time();
        if(!$datas || date('G') == 0) {
            $appRepository = new AppRepository();
            $apps = $appRepository->getAppsForRand();
            if(empty($apps)) return [];
            $surplus_time = mktime(0, 0, 0, date('m'), date('j') + 1, date('Y')) - $now;
            $datas = [];
            for($i = 0; $i < $max; $i++) {
                if($empty && rand(1, $probability) == 1) {
                    $datas['data'][] = '';
                }else {
                    $mobile = $this->getRandMobile();
                    $app = $apps[rand(0, (count($apps) - 1))];
                    $moneys = json_decode($app['moneys'], true);
                    $money = $moneys[rand(0, (count($moneys) - 1))];
                    $datas['data'][] = $mobile . '在' . $app['name'] . '成功贷款' . $money . '元';
                }
            }
            if(empty($datas['data'])) return [];
            $datas['time'] = $now;
            Cache::put($key, $datas, ceil($surplus_time / 60));
        }
        $start_number = round(($now - $datas['time']) / $timer) % $max;
        if(!isset($datas['data'][$start_number])) $start_number = 0;
        return $start_number == 0 ? $datas['data'] : 
            array_merge(
                array_slice($datas['data'], $start_number, ($max - $start_number)), 
                array_slice($datas['data'], 0, $start_number)
            );
    }

    public function getRandMobile()
    {
        $before = [
            '130', '131', '132', '133', '134', '135', '136', '137', '138', '139',
            '145', '147', 
            '150', '151', '152', '153', '155', '156', '157', '158', '159',
            '176', '177',
            '180', '181', '182', '183', '184', '185', '186', '187', '188', '189',
        ];
        $after = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        return $before[rand(0, (count($before) - 1))] . '****' . $after;
    }
}
