<?php
namespace App\Repositories\Api;

use App\Models\App as AppModel;
use Illuminate\Support\Facades\DB;

class AppRepository
{
    public function getAppByInId(Array $ids = [], $sort_str)
    {
        $origin_apps = AppModel::whereIn('id', $ids)
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
                ->orderByRaw($sort_str)
                ->get()->toArray();
        $target_apps = [];
        $rate_types = ['日', '周', '月', '年'];
        foreach($origin_apps as $key => $val) {
            $moneys = json_decode($val['moneys'], true);
            $terms = json_decode($val['terms'], true);
            $app = [
                'id' => $val['id'],
                'name' => $val['name'],
                'icon' => url('storage/' . $val['icon']),
                'money_max' => max($moneys),
                'money_rand' => $this->get_rand_string($moneys, true),
                'term_rand' => $terms[0]['value'] . $terms[0]['type'],
                'apply_number' => $val['apply_number'],
                'synopsis' => $val['synopsis'],
                'rate' => $val['rate'],
                'rate_type_name' => $rate_types[$val['rate_type']],
            ];
            if(count($terms) > 1) {
                $last_term = $terms[count($terms) - 1];
                $app['term_rand'] .= ('-' . $last_term['value'] . $last_term['type']);
            }
            $target_apps[] = $app;
        }

        return $target_apps;
    }

    public function get_rand_string(Array $data, $sort = false)
    {
        $str = '';
        if(!empty($data)) {
            if($sort) sort($data);
            $str = $data[0];
            if(count($data) > 1) {
                $str .= ('-' . $data[count([$data]) - 1]);
            }
        }
        return $str;
    }
}
