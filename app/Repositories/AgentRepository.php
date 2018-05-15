<?php
namespace App\Repositories;

use App\Models\Agent;
use App\Models\User;
use App\Http\Controllers\ApiResponse;
use App\Services\Formatquery;

class AgentRepository
{
    use ApiResponse;

    protected $agent;
    
    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function getList($request = [])
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
        );
        $formatquery = new Formatquery($config);
        $query = $formatquery->setParams($request)->getParams();
        // error_log(print_r($query, true));
        $mysql = $this->agent->whereRaw($query['whereStr'] ? $query['whereStr'] : 1);
        $ret = [
            'total' => $mysql->count(),
            'rows' => [],
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

            $types = config('my.site.recomm_types');
            $users = User::select('id', 'status', 'recomm_id', 'activated_at', 'created_at')
                ->where('recomm_type', array_search('agents', $types))
                ->whereIn('recomm_id', $ids)
                ->get()
                ->toArray();
            $agent_count = [];
            foreach($users as $key => $val) {
                if(!isset($agent_count[$val['recomm_id']])) $agent_count[$val['recomm_id']] = [];
                if(!isset($agent_count[$val['recomm_id']]['register'])) $agent_count[$val['recomm_id']]['register'] = 0;
                if(!isset($agent_count[$val['recomm_id']]['activate'])) $agent_count[$val['recomm_id']]['activate'] = 0;
                $agent_count[$val['recomm_id']]['register']++;
                if(isset($val['activated_at'])) $agent_count[$val['recomm_id']]['activate']++;
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

    public function getUserByAgent($id, $request = [])
    {
        
    }
}
