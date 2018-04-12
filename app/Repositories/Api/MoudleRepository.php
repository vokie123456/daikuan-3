<?php
namespace App\Repositories\Api;

use App\Models\App as AppModel;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class MoudleRepository
{
    public function getBanner()
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

    public function getCategory($type = 1)
    {
        $categories = Category::where('type', $type)
                ->select(['id', 'name', 'image'])
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->orderBy('created_at', 'asc')
                ->get()->toArray();
        foreach($categories as $key => $val) {
            if(!empty($val['image'])) {
                $categories[$key]['image'] = url('storage/' . $val['image']);
            }
        }
        return $categories;
    }

    public function getApps($type = 0)
    {
        $categories = Category::with('apps')
                ->select(['id', 'name', 'sort_app'])
                ->where('type', $type)->where('status', 1)
                ->orderBy('sort', 'asc')
                ->orderBy('created_at', 'asc')
                ->get()->toArray();
        $rate_types = ['日', '周', '月', '年'];
        foreach($categories as $key => $val) {
            if(!empty($val['apps'])) {
                $apps_id = array_map(function($item) {return $item['app_id'];}, $val['apps']);
                $sorts = [
                    '`created_at` desc', 
                    '`created_at` asc', 
                    '`sort` desc, `created_at` desc', 
                    '`sort` asc, `created_at` desc',
                ];
                $apps = AppModel::whereIn('id', $apps_id)
                        ->select(
                            'id', 
                            'name', 
                            'icon', 
                            'apply_number', 
                            'synopsis', 
                            'rate', 
                            'rate_type',
                            'moneys',
                            'terms'
                        )
                        ->orderByRaw($sorts[$val['sort_app']])
                        ->get()->toArray();
                foreach($apps as $a => $app) {
                    $apps[$a]['icon'] = url('storage/' . $app['icon']);
                    $apps[$a]['money_max'] = max(json_decode($app['moneys'], true));
                    $apps[$a]['terms'] = json_decode($app['terms'], true);
                    $apps[$a]['rate_type_name'] = $rate_types[$app['rate_type']];
                    $terms = json_decode($app['terms'], true);
                    $apps[$a]['term_str'] = $terms[0]['value'] . $terms[0]['type'];
                    if(count($terms) > 1) {
                        $last_term = $terms[count($terms) - 1];
                        $apps[$a]['term_str'] .= ('-' . $last_term['value'] . $last_term['type']);
                    }
                }
                $categories[$key]['apps'] = $apps;
            }
        }

        return $categories;
    }
}
