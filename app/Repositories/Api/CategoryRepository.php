<?php
namespace App\Repositories\Api;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Repositories\Api\AppRepository;

class CategoryRepository
{
    protected $appRepository;
    
    public function __construct(AppRepository $appRepository)
    {
        $this->appRepository = $appRepository;
    }

    /**
     * 通过type类型, 获取该类型下的信息
     * 
     * @param  Int    $type        类型id
     * @param  Bool   $withApp     是否附带查询该类型下的APP
     * @param  Array
     */
    public function getCategoryByType($type, $withApp = true)
    {
        $query = Category::select([
                'id', 
                'name', 
                'image', 
                // [原代码]
                // 'sort_app',
            ])
            ->where('type', $type)->where('status', 1)
            ->orderBy('sort', 'asc')->orderBy('created_at', 'asc');
        // [原代码]
        // if($withApp) $query = $query->with('apps');
        $categories = $query->get()->toArray();
        
        foreach($categories as $key => $val) {
            if(!empty($val['image'])) $categories[$key]['image'] = url(config('my.site.storage') . $val['image']);
            // [原代码]
            // if(!empty($val['apps'])) {
            //     $apps_id = array_map(function($item) {return $item['app_id'];}, $val['apps']);
            //     $categories[$key]['apps'] = $this->appRepository->getAppByInId($apps_id, $val['sort_app'], false);
            // }
            // if(isset($val['sort_app'])) unset($categories[$key]['sort_app']);

            // [新代码]
            if($withApp) {
                $categories[$key]['apps'] = $this->categoryWithApps($val['id'], false);
            }
        }
        return $categories;
    }

    public function categoryWithApps($cateid, $isPaginate)
    {
        $query = DB::table('category_apps AS ca')
            ->select(
                'a.id', 
                'a.name', 
                'a.icon', 
                'a.apply_number', 
                'a.synopsis', 
                'a.rate',
                'a.rate_type',
                'a.moneys',
                'a.terms',
                'a.marks',
                'a.isNew'
            )
            ->leftJoin('apps AS a', 'a.id', '=', 'ca.app_id')
            ->where('ca.category_id', $cateid)
            ->where('a.status', 1)
            ->orderBy('ca.sort', 'desc')
            ->orderBy('a.created_at', 'desc');
        return $this->appRepository->getDatasByQuery($query, $isPaginate);
    }

    /**
     * 通过类别id, 获取该类别下的APP
     * 
     * @param  Int    $id          id
     * @param  Bool   $isPaginate  是否分页
     * @param  Array
     */
    public function getCategoryAppById($id, $isPaginate = true)
    {
        $category = Category::select([
                'id', 
                'name', 
                'image', 
                // [原代码]
                // 'sort_app',
            ])
            // [原代码]
            // ->with('apps')
            ->where('id', $id)->where('status', 1)
            ->first();
        if($category) {
            $category = $category->toArray();
            if(!empty($category['image'])) $category['image'] = url(config('my.site.storage') . $category['image']);
            // [原代码]
            // if(!empty($category['apps'])) {
            //     $apps_id = array_map(function($item) {return $item['app_id'];}, $category['apps']);
            //     $category['apps'] = $this->appRepository->getAppByInId($apps_id, $category['sort_app'], $isPaginate);
            // }else if($isPaginate) {
            //     $category['apps']['data'] = [];
            // }
            // if(isset($category['sort_app'])) unset($category['sort_app']);

            // [新代码]
            $category['apps'] = $this->categoryWithApps($category['id'], $isPaginate);
        }
        
        return $category;
    }
}
