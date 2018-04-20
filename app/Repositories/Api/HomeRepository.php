<?php
namespace App\Repositories\Api;

use App\Repositories\Api\CategoryRepository;
use App\Repositories\BannerRepository;
use App\Http\Resources\Api\BannerResource;

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
        return BannerResource::collection($data);
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
        ];
    }
}
