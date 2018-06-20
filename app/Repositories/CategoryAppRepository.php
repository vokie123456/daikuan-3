<?php

namespace App\Repositories;

use App\Models\CategoryApp;
use Illuminate\Support\Facades\Storage;
use App\Models\App as AppModel;

class CategoryAppRepository
{
    protected $categoryapp;
    
    public function __construct(CategoryApp $categoryapp)
    {
        $this->categoryapp = $categoryapp;
    }

    public function getDatas($category_id)
    {
        $apps = AppModel::select('id', 'name', 'icon', 'created_at', 'status')
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        $arrs = $this->categoryapp->where('category_id', (int)$category_id)->get()->toArray();
        $selected_app = [];
        foreach($arrs as $val) {
            $selected_app[$val['app_id']] = $val;
        }
        foreach($apps as $key => $app) {
            $apps[$key]['appicon'] = ($app['icon'] && Storage::disk('public')->exists($app['icon'])) ? 
                Storage::url($app['icon']) : asset('images/no_image.png');
            $apps[$key]['is_checked'] = isset($selected_app[$app['id']]) ? true : false;
            $apps[$key]['sort'] = isset($selected_app[$app['id']]) ? $selected_app[$app['id']]['sort'] : 0;
        }
        return $apps;
    }

    public function setDatas($category_id, Array $data)
    {
        $this->categoryapp->where('category_id', $category_id)->delete();
        $datas = [];
        foreach($data as $val) {
            $datas[] = [
                'app_id' => (int)$val['app_id'],
                'category_id' => (int)$category_id,
                'sort' => isset($val['sort']) ? (int)$val['sort'] : 0,
            ];
        }
        if(!empty($datas)) {
            $this->categoryapp->insert($datas);
        }
    }
}
