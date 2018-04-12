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
        $this->app = new AppRepository();
    }

    public function getCategoryByType($type, $with_app = true)
    {
        $query = Category::select(['id', 'name', 'image', 'sort_app']);
        if($with_app) $query = $query->with('apps');

        $categories = $query->where('type', $type)->where('status', 1)
                ->orderBy('sort', 'asc')
                ->orderBy('created_at', 'asc')
                ->get()->toArray();
        $sorts = [
            '`created_at` desc', 
            '`created_at` asc', 
            '`sort` desc, `created_at` desc', 
            '`sort` asc, `created_at` desc',
        ];
        foreach($categories as $key => $val) {
            if(!empty($val['image'])) {
                $categories[$key]['image'] = url('storage/' . $val['image']);
            }
            if(!empty($val['apps'])) {
                $apps_id = array_map(function($item) {return $item['app_id'];}, $val['apps']);
                $_sort = isset($sorts[$val['sort_app']]) ? $sorts[$val['sort_app']] : $sorts[0];
                $categories[$key]['apps'] = $this->app->getAppByInId($apps_id, $_sort);
            }
        }
        return $categories;
    }
}
