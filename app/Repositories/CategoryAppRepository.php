<?php

namespace App\Repositories;

use App\Models\CategoryApp;
use App\Models\App as AppModel;

class CategoryAppRepository
{
    protected $app;
    protected $categoryapp;
    
    public function __construct(AppModel $app, CategoryApp $categoryapp)
    {
        $this->app = $app;
        $this->categoryapp = $categoryapp;
    }

    public function getDatas($category_id)
    {
        $apps = $this->app::with('company')->orderBy('created_at', 'desc')->get();
        $selected_app_id = $this->categoryapp->where('category_id', (int)$category_id)->pluck('app_id');
        $selected_app_id = $selected_app_id ? $selected_app_id->toArray() : [];
        $source = [];
        $target = [];
        if(!empty($apps)) {
            foreach($apps as $app) {
                $source[] = array(
                    'key' => $app['id'],
                    'app_name' => $app['name'],
                    'company_name' => $app['company']['name'],
                );
                if(!empty($selected_app_id) && in_array($app['id'], $selected_app_id)) {
                    $target[] = $app['id'];
                }
            }
        }
        return [
            'source' => $source,
            'target' => $target,
        ];
    }

    public function setDatas($category_id, Array $selected)
    {
        $this->categoryapp->where('category_id', $category_id)->delete();
        $datas = [];
        foreach($selected as $val) {
            $datas[] = [
                'app_id' => $val,
                'category_id' => $category_id,
            ];
        }
        if(!empty($datas)) {
            $this->categoryapp->insert($datas);
        }
    }
}
