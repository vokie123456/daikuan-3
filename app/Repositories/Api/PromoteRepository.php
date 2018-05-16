<?php
namespace App\Repositories\Api;

use App\Models\Promote;
use App\Services\Formatquery;

class PromoteRepository
{
    protected $promote;
    
    public function __construct(Promote $promote)
    {
        $this->promote = $promote;
    }

    public function getList($request = []) 
    {
        $config = array(
            'defSort'   => 'promotes.created_at',
            'defOrder'  => 'desc',
            'sortArr'   => array(
                'created_at' => 'promotes.created_at',
                'telephone' => 'users.telephone',
                'appname' => 'apps.name',
            ),
            'searchArr' => array(
                'name'  => [
                    'alias' => 'dk_users.telephone',
                ],
                'stime' => [
                    'alias' => 'dk_promotes.created_at',
                    'rule' => '%alias% >= \'%s\'',
                ],
                'etime' => [
                    'alias' => 'dk_promotes.created_at',
                    'rule' => '%alias% <= \'%s\'',
                ],
            ),
        );
        $formatquery = new Formatquery($config);
        $query = $formatquery->setParams($request)->getParams();
        // error_log(print_r($query, true));
        $mysql = $this->promote->whereRaw($query['whereStr'] ? $query['whereStr'] : 1)
            ->leftJoin('users', 'users.id', '=', 'promotes.user_id');
        $ret = [
            'total' => $mysql->count(),
            'rows' => [],
        ];
        if($ret['total']) {
            $ret['rows'] = $mysql
                ->leftJoin('apps', 'apps.id', '=', 'promotes.app_id')
                ->select(
                    'promotes.*',
                    'users.telephone',
                    'apps.name AS appname'
                )
                ->orderBy($query['sort'], $query['order'])
                ->skip($query['offset'])
                ->take($query['limit'])
                ->get();
        }
        return $ret;
    }

    public function click_promote($app_id, $user_id)
    {
        $row = $this->promote->where('app_id', $app_id)
            ->where('user_id', $user_id)
            ->where('created_at', '>', date('Y-m-d'))->first();
        if(!$row) {
            $this->promote->insert(compact('app_id', 'user_id'));
        }
    }

    public function getCountByToday()
    {
        return $this->promote->where('created_at', '>', date('Y-m-d'))->count();
    }

    public function getCountByDate($date)
    {
        return $this->promote->where('created_at', '>', $date)->get()->toArray();
    }
}
