<?php
namespace App\Repositories\Api;

use App\Models\App as AppModel;

class AppRepository
{
    /**
     * 通过app的多个id, 获取APPs
     * 
     * @param  Int    $ids         ids
     * @param  Int    $sort        排序分式: 0, 1, 2, 3
     * @param  Bool   $isPaginate  是否分页
     * @param  Array
     */
    public function getAppByInId(Array $ids = [], $sort, $isPaginate = true)
    {
        $sorts = [
            '`created_at` desc', 
            '`created_at` asc', 
            '`sort` desc, `created_at` desc', 
            '`sort` asc, `created_at` desc',
        ];
        $_sort = isset($sorts[$sort]) ? $sorts[$sort] : $sorts[0];
        $query = AppModel::whereIn('id', $ids)
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
                ->orderByRaw($_sort);
        $origin_apps = ($isPaginate ? $query->simplePaginate(15) : $query->get())->toArray();
        $rate_types = ['日', '周', '月', '年'];
        $target_apps = [];
        $datas = $isPaginate ? $origin_apps['data'] : $origin_apps;
        foreach($datas as $key => $val) {
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
                'rate' => floatval($val['rate']),
                'rate_type_name' => $rate_types[$val['rate_type']],
            ];
            if(count($terms) > 1) {
                $last_term = $terms[count($terms) - 1];
                $app['term_rand'] .= ('-' . $last_term['value'] . $last_term['type']);
            }

            if($isPaginate) {
                $target_apps['data'][] = $app;
            }else {
                $target_apps[] = $app;
            }
        }

        if($isPaginate) {
            $target_apps['current_page'] = $origin_apps['current_page'];
            $target_apps['per_page'] = $origin_apps['per_page'];
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
