<?php
namespace App\Repositories;

use App\Models\Agent;
use App\Models\User;
use App\Http\Controllers\ApiResponse;
use App\Services\Formatquery;
use Illuminate\Support\Facades\DB;

class AgentRepository
{
    use ApiResponse;

    protected $agent;
    
    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function getList($request = [], $parentid = null)
    {
        $config = array(
            'defSort'   => 'created_at',
            'defOrder'  => 'desc',
            'sortArr'   => array(
                'created_at',
                'name',
            ),
            'searchArr' => array(
                'name'  => ['rule' => '%alias% like \'%%s%\'',],
            ),
            'byKeyToArray' => 'name',
        );
        $formatquery = new Formatquery($config);
        $query = $formatquery->setParams($request)->getParams();
        // error_log(print_r($query, true));
        $mysql = $this->agent->whereRaw($query['whereStr'] ? $query['whereStr'] : 1);
        if($parentid) {
            $mysql = $mysql->whereIn('id', $this->getChilds($parentid));
        }else if(isset($request['junior']) && $request['junior'] && isset($query['searchs']['name'])) {
            //搜索添加下级
            $finds = $mysql->pluck('id')->toArray();
            if(!empty($finds)) {
                $mysql = $this->agent->whereIn('id', $this->getChilds($finds));
            }
        }
        $starttime = isset($request['stime']) ? $request['stime'] : null;
        $endtime = isset($request['etime']) ? $request['etime'] : null;
        $search_users = $this->getUserByAgentIds($mysql->pluck('id')->toArray(), $starttime, $endtime);
        $total_register = 0;
        $total_activate = 0;
        foreach($search_users as $user) {
            if($this->checkDateBetween($user['created_at'], $starttime, $endtime)) {
                $total_register++;
            }
            if(isset($user['activated_at']) && $this->checkDateBetween($user['activated_at'], $starttime, $endtime)) {
                $total_activate++;
            }
        }
        $ret = [
            'total' => $mysql->count(),
            'rows' => [],
            'total_register' => $total_register,
            'total_activate' => $total_activate,
        ];
        if($ret['total']) {
            $ret['rows'] = $mysql->with('parent:id,name')
                ->select('id', 'name', 'username', 'created_at', 'parent_id', 'note')
                ->orderBy($query['sort'], $query['order'])
                ->skip($query['offset'])
                ->take($query['limit'])
                ->get()
                ->toArray();

            $ids = array_map(function($item) {
                return $item['id'];
            }, $ret['rows']);
            $users = $this->getUserByAgentIds($ids, $starttime, $endtime);
            $agent_count = [];
            foreach($users as $key => $val) {
                if(!isset($agent_count[$val['recomm_id']])) $agent_count[$val['recomm_id']] = [];
                if(!isset($agent_count[$val['recomm_id']]['register'])) $agent_count[$val['recomm_id']]['register'] = 0;
                if(!isset($agent_count[$val['recomm_id']]['activate'])) $agent_count[$val['recomm_id']]['activate'] = 0;
                if($this->checkDateBetween($val['created_at'], $starttime, $endtime)) {
                    $agent_count[$val['recomm_id']]['register']++;
                }
                if(isset($val['activated_at']) && $this->checkDateBetween($val['activated_at'], $starttime, $endtime)) {
                    $agent_count[$val['recomm_id']]['activate']++;
                }
            }
            $share_url = config('my.site.register_path');
            $recom_key = config('my.site.recomm');
            foreach($ret['rows'] as $key => $val) {
                $ret['rows'][$key]['register'] = isset($agent_count[$val['id']]) ? $agent_count[$val['id']]['register'] : 0;
                $ret['rows'][$key]['activate'] = isset($agent_count[$val['id']]) ? $agent_count[$val['id']]['activate'] : 0;
                $code = create_url_encode_by_id('agents', $val['id']);
                $ret['rows'][$key]['share_url'] = $share_url . "?{$recom_key}=" . $code;
            }
        }
        return $ret;
    }

    //验证日期是否在指定日期内
    public function checkDateBetween($date, $sdate, $edate) {
        if(
            (!$sdate || ($sdate && $sdate <= $date)) &&
            (!$edate || ($edate && $edate >= $date))
        ) {
            return true;
        }
        return false;
    }

    public function getUserByAgentIds($ids, $stime = null, $etime = null) {
        $types = config('my.site.recomm_types');
        $mysql = User::select('id', 'status', 'recomm_id', 'activated_at', 'created_at')
        ->where('recomm_type', array_search('agents', $types))
        ->whereIn('recomm_id', $ids);
        if($stime && strtotime($stime)) {
            $mysql = $mysql->whereRaw('(activated_at >= ? OR created_at >= ?)', [$stime, $stime]);
        }
        if($etime && strtotime($etime)) {
            $mysql = $mysql->whereRaw('(activated_at <= ? OR created_at <= ?)', [$etime, $etime]);
        }
        return $mysql->get()->toArray();
    }

    public function getAll()
    {
        return $this->agent->select('id', 'name')->orderBy('name', 'asc')->get();
    }

    public function create($datas)
    {
        $agent = [
            'name' => $datas['name'],
            'username' => $datas['username'],
            'password' => bcrypt($datas['password']),
            'note' => isset($datas['note']) ? $datas['note'] : '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if($this->agent->where('username', $datas['username'])->first()) {
            return $this->set_error('该登录名已被注册!');
        }
        if(!empty($datas['parent_id'])) {
            $parent = $this->agent->find($datas['parent_id']);
            if(!$parent) {
                return $this->set_error('找不到该上级!');
            }
            $agent['parent_id'] = $parent->id;
        }
        $ret = $this->agent->create($agent);
        if($ret) {
            return $this->set_success('添加成功!');
        }else {
            return $this->set_error('添加失败!');
        }
    }

    public function getAgentByName($name)
    {
        return $this->agent->where('username', $name)->first();
    }

    public function getAgentById($id)
    {
        return $this->agent->where('id', $id)->first();
    }

    public function getAgentByParentId($id)
    {
        return $this->agent->select('id', 'name')->where('id', $id)->orWhere('parent_id', $id)->get();
    }

    public function getChilds($id)
    {
        $ids = [];
        if(is_array($id)) {
            $ids = $this->agent->whereIn('parent_id', $id)->pluck('id')->toArray();
            return array_merge($id, $ids);
        }else {
            $ids = $this->agent->where('parent_id', $id)->pluck('id')->toArray();
        }
        $ids[] = $id;
        return $ids;
    }

    public function getUserByAgent($id, $request = [])
    {
        $ids = $this->getChilds($id);
        $config = array(
            'defSort'   => 'users.created_at',
            'defOrder'  => 'desc',
            'sortArr'   => array(
                'created_at' => 'users.created_at',
                'activated_at' => 'users.activated_at',
            ),
            'searchArr' => array(
                'startTime' => array(
                    'alias' => 'dk_users.created_at',
                    'rule' => '(%alias% >= \'%s\' OR dk_users.activated_at >= \'%s\')',
                ),
                'endTime' => array(
                    'alias' => 'dk_users.created_at',
                    'rule' => '(%alias% <= \'%s\' OR dk_users.activated_at <= \'%s\')',
                ),
                'isActive' => array(
                    'alias' => 'dk_users.activated_at',
                    'allow' => [1, 2],
                    'myfunction' => function($val) {
                        if($val == 1) {
                            return '%alias% IS NOT NULL';
                        }else if($val == 2) {
                            return '%alias% IS NULL';
                        }
                        return null;
                    },
                ),
            ),
        );
        $parent_id = intval($request['parent']);
        $formatquery = new Formatquery($config);
        $query = $formatquery->setParams($request)->getParams();
        $types = config('my.site.recomm_types');
        $mysql = DB::table('users')
            ->where('users.recomm_type', array_search('agents', $types))
            ->whereRaw(
                ($parent_id && in_array($parent_id, $ids)) ? 
                "`dk_users`.`recomm_id` = {$parent_id}" :
                "`dk_users`.`recomm_id` in(" . implode(',', $ids) . ")"
            )
            ->whereRaw($query['whereStr'] ? $query['whereStr'] : 1);
        $count_where = implode(' AND ', array_filter($query['wheres'], function($val) {
            return !strpos($val, 'dk_users.activated_at');
        }));
        $register_mysql = DB::table('users')
        ->where('users.recomm_type', array_search('agents', $types))
        ->whereRaw(
            ($parent_id && in_array($parent_id, $ids)) ? 
            "`dk_users`.`recomm_id` = {$parent_id}" :
            "`dk_users`.`recomm_id` in(" . implode(',', $ids) . ")"
        )->whereRaw($count_where ? $count_where : 1);
        $activate_mysql = DB::table('users')
        ->where('users.recomm_type', array_search('agents', $types))
        ->whereRaw(
            ($parent_id && in_array($parent_id, $ids)) ? 
            "`dk_users`.`recomm_id` = {$parent_id}" :
            "`dk_users`.`recomm_id` in(" . implode(',', $ids) . ")"
        )->whereRaw($count_where ? $count_where : 1)
        ->whereRaw('dk_users.activated_at IS NOT NULL');
        $stime = isset($query['searchs']['startTime']) ? $query['searchs']['startTime'] : null;
        $etime = isset($query['searchs']['endTime']) ? $query['searchs']['endTime'] : null;
        if($stime && strtotime($stime)) {
            $register_mysql = $register_mysql->where('users.created_at', '>=', $stime);
            $activate_mysql = $activate_mysql->where('users.activated_at', '>=', $stime);
        }
        if($etime && strtotime($etime)) {
            $register_mysql = $register_mysql->where('users.created_at', '<=', $etime);
            $activate_mysql = $activate_mysql->where('users.activated_at', '<=', $etime);
        }
        $ret = [
            'total' => $mysql->count(),
            'rows' => [],
            'register_total' => $register_mysql->count(),
            'activate_total' => $activate_mysql->count(),
        ];
        if($ret['total']) {
            $ret['rows'] = $mysql
                ->leftJoin('agents', 'agents.id', '=', 'users.recomm_id')
                ->select(
                    'users.id', 
                    'users.telephone',
                    'agents.name AS agentname', 
                    'users.activated_at', 
                    'users.created_at'
                )
                ->orderBy($query['sort'], $query['order'])
                ->skip($query['offset'])
                ->take($query['limit'])
                ->get();
        }
        return $ret;
    }
}
