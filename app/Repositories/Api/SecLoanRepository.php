<?php
namespace App\Repositories\Api;

use App\Repositories\Api\CategoryRepository;
use App\Repositories\BannerRepository;
use App\Http\Resources\Api\BannerResource;

class SecLoanRepository
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
                'position' => array_search(config('my.site.moudle_type')[3], config('my.site.banner_position')),
                'show_time' => date('Y-m-d H:i:s'),
                'status' => 1,
            ],
        ];
        $data = $this->banner->getList($query);
        return BannerResource::collection($data['rows']);
    }

    public function getDatas()
    {
        $datas = [
            'banner' => $this->getBanner(),
            'category' => null,
        ];
        $categories = $this->category->getCategoryByType(3, false);
        if(isset($categories[0])) {
            $datas['category'] = $this->category->getCategoryAppById($categories[0]['id'], true);
        }
        return $datas;
    }
}
