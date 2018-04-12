<?php
namespace App\Repositories\Api;

use App\Repositories\Api\CategoryRepository;

class MoudleRepository
{
    protected $category;
    
    public function __construct()
    {
        $this->category = new CategoryRepository();
    }

    protected function getBanner()
    {
        return [
            [
                'id' => 1,
                'name' => '测试广告1',
                'type' => 0,
                'app_id' => 12,
                'url' => '#',
                'image' => url('storage/banner/1-01-1.png'),
                'start_time' => '2018-04-10 14:02:13',
                'end_time' => '2018-06-10 14:02:13',
            ], [
                'id' => 3,
                'name' => '测试广告2',
                'type' => 0,
                'app_id' => 13,
                'url' => '#',
                'image' => url('storage/banner/1-01-2.png'),
                'start_time' => '2018-04-10 14:02:13',
                'end_time' => '2018-06-10 14:02:13',
            ], [
                'id' => 4,
                'name' => '测试广告3',
                'type' => 0,
                'app_id' => 14,
                'url' => '#',
                'image' => url('storage/banner/1-01-3.png'),
                'start_time' => '2018-04-10 14:02:13',
                'end_time' => '2018-06-10 14:02:13',
            ],
        ];
    }

    protected function getHomeIcon()
    {
        return $this->category->getCategoryByType(1, false);
    }

    protected function getApps($type = 0)
    {
        return $this->category->getCategoryByType($type);
    }

    public function getDatas()
    {
        return [
            'banners' => $this->getBanner(),
            'icons' => $this->getHomeIcon(),
            'category_apps' => $this->getApps(),
        ];
    }
}
