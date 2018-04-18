<?php
namespace App\Repositories\Api;

use App\Models\App as AppModel;

class AppRepository
{
    public function getAppById($id)
    {
        $app = AppModel::select(
            'id', 
            'name', 
            'icon', 
            'marks',
            'recommend',
            'apply_number', 
            'synopsis', 
            'details',
            'rate',
            'rate_type',
            'moneys',
            'terms',
            'repayments',
            'status',
            'isNew'
        )->where('id', $id)->first();
        if($app) {
            $app = $app->toArray();
            $moneys = json_decode($app['moneys'], true);
            $terms = json_decode($app['terms'], true);
            $rate_types = config('my.site.rate_types');

            $app['icon'] = url(config('my.site.storage') . $app['icon']);
            $app['money_max'] = max($moneys);
            $app['money_rand'] = $this->get_rand_string($moneys, true);
            $app['term_rand'] = $terms[0]['value'] . $terms[0]['type'];
            $app['rate'] = floatval($app['rate']);
            $app['rate_type_name'] = $rate_types[$app['rate_type']];
            $app['repayments'] = json_decode($app['repayments'], true);
            if(count($terms) > 1) {
                $last_term = $terms[count($terms) - 1];
                $app['term_rand'] .= ('/' . $last_term['value'] . $last_term['type']);
            }
        }
        return $app;
    }

    /**
     * 通过app的多个id, 获取APPs
     * 
     * @param  Int    $ids         ids
     * @param  Int    $sort        排序分式: 0, 1, 2, 3
     * @param  Bool   $isPaginate  是否分页
     * @param  Array
     */
    public function getAppByInId(Array $ids = [], $sort = 0, $isPaginate = true)
    {
        $sorts = [
            '`created_at` desc', 
            '`created_at` asc', 
            '`sort` desc, `created_at` desc', 
            '`sort` asc, `created_at` desc',
        ];
        $_sort = ($sort && isset($sorts[$sort])) ? $sorts[$sort] : $sorts[0];
        $query = AppModel::where('status', 1)->whereIn('id', $ids)
                ->select(
                    'id', 
                    'name', 
                    'icon', 
                    'apply_number', 
                    'synopsis', 
                    'rate',
                    'rate_type',
                    'moneys',
                    'terms',
                    'marks',
                    'isNew'
                )
                ->orderByRaw($_sort);
        $origin_apps = ($isPaginate ? $query->simplePaginate(15) : $query->get())->toArray();
        $rate_types = config('my.site.rate_types');
        $target_apps = ['data' => []];
        $datas = $isPaginate ? $origin_apps['data'] : $origin_apps;
        foreach($datas as $key => $val) {
            $moneys = json_decode($val['moneys'], true);
            $terms = json_decode($val['terms'], true);
            $app = [
                'id' => $val['id'],
                'name' => $val['name'],
                'icon' => url(config('my.site.storage') . $val['icon']),
                'money_max' => max($moneys),
                'money_rand' => $this->get_rand_string($moneys, true),
                'term_rand' => $terms[0]['value'] . $terms[0]['type'],
                'apply_number' => $val['apply_number'],
                'synopsis' => $val['synopsis'],
                'rate' => floatval($val['rate']),
                'rate_type_name' => $rate_types[$val['rate_type']],
                'marks' => (isset($val['marks'])) ? json_decode($val['marks'], true) : null,
                'isNew' => isset($val['isNew']) ? $val['isNew'] : 0,
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

        if($isPaginate && count($target_apps['data'])) {
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
