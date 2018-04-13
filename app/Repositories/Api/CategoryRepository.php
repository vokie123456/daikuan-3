<?php
namespace App\Repositories\Api;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Repositories\Api\AppRepository;

class CategoryRepository
{
    protected $app;
    
    public function __construct()
    {
        // 这里不能使用注入
        $this->app = new AppRepository();
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
        $query = Category::select(['id', 'name', 'image', 'sort_app'])
            ->where('type', $type)->where('status', 1)
            ->orderBy('sort', 'asc')->orderBy('created_at', 'asc');
        if($withApp) $query = $query->with('apps');
        $categories = $query->get()->toArray();
        
        foreach($categories as $key => $val) {
            if(!empty($val['image'])) $categories[$key]['image'] = url(config('my.site.storage') . $val['image']);
            if(!empty($val['apps'])) {
                $apps_id = array_map(function($item) {return $item['app_id'];}, $val['apps']);
                $categories[$key]['apps'] = $this->app->getAppByInId($apps_id, $val['sort_app'], false);
            }
            if(isset($val['sort_app'])) unset($categories[$key]['sort_app']);
        }
        return $categories;
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
        $category = Category::with('apps')
            ->select(['id', 'name', 'image', 'sort_app'])
            ->where('id', $id)->where('status', 1)
            ->first();
        if($category) {
            $category = $category->toArray();
            if(!empty($category['image'])) $category['image'] = url(config('my.site.storage') . $category['image']);
            if(!empty($category['apps'])) {
                $apps_id = array_map(function($item) {return $item['app_id'];}, $category['apps']);
                $category['apps'] = $this->app->getAppByInId($apps_id, $category['sort_app'], $isPaginate);
            }
            if(isset($category['sort_app'])) unset($category['sort_app']);
        }
        
        return $category;
    }
}
