<?php
namespace App\Repositories\Api;

use App\Models\App as AppModel;
use App\Http\Resources\Api\AppListResource;
use Illuminate\Support\Facades\Log;

class AppRepository
{
    public function getAppsForRand()
    {
        return AppModel::select('name', 'moneys')
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->take(100)
                ->get()
                ->toArray();
    }

    public function getSimpleAppById($id)
    {
        return AppModel::select('id', 'weburl', 'status')->where('id', $id)->first();
    }

    public function getAppById($id)
    {
        $app = AppModel::select(
            'id', 
            'name', 
            'icon',
            'recommend',
            'apply_number', 
            'synopsis', 
            'details',
            'rate',
            'rate_type',
            'moneys',
            'terms',
            'repayments',
            'status'
        )->where('id', $id)->first();
        if($app) {
            $app = $app->toArray();
            $moneys = json_decode($app['moneys'], true);
            $terms = json_decode($app['terms'], true);
            $rate_types = config('my.site.rate_types');
            $app['recommend'] = round($app['recommend'] / 2, 1);
            $app['icon'] = url(config('my.site.storage') . $app['icon']);
            $app['money_max'] = max($moneys);
            $app['money_rand'] = $this->get_rand_string($moneys, true);
            $app['term_rand'] = $terms[0]['value'] . $terms[0]['type'];
            $app['rate'] = floatval($app['rate']);
            $app['rate_type_name'] = $rate_types[$app['rate_type']];
            $app['repayments'] = implode('/', json_decode($app['repayments'], true));
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
        $origin_apps = $isPaginate ? $query->simplePaginate(15) : $query->get();
        $_origin_apps = $origin_apps->toArray();
        $target_apps = [];
        if($isPaginate) $target_apps['data'] = [];
        if($isPaginate) {
            $target_apps['data'] = AppListResource::collection($origin_apps);
        }else {
            $target_apps = AppListResource::collection($origin_apps);
        }

        if($isPaginate && count($_origin_apps['data'])) {
            $target_apps['current_page'] = $_origin_apps['current_page'];
            $target_apps['per_page'] = $_origin_apps['per_page'];
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
                $str .= ('-' . $data[count($data) - 1]);
            }
        }
        return $str;
    }
}
