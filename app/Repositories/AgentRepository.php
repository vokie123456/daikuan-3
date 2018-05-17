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
        }
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
                ->whereIn('recomm_id', $ids);
            if(isset($request['stime']) && strtotime($request['stime'])) {
                $users = $users->whereRaw('(activated_at >= ? OR created_at >= ?)', [$request['stime'], $request['stime']]);
            }
            if(isset($request['etime']) && strtotime($request['etime'])) {
                $users = $users->whereRaw('(activated_at <= ? OR created_at <= ?)', [$request['etime'], $request['etime']]);
            }
            $users = $users->get()->toArray();
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
        $ids = $this->agent->where('parent_id', $id)->pluck('id')->toArray();
        $ids[] = $id;
        return $ids;
    }

    public function getUserByAgent($id, $request = [])
    {
        $ids = $this->getChilds($id);
        $config = array(
            'defSort'   => 'users.created_at',
            'defOrder'  => 'desc',
            'searchArr' => array(
                'startTime' => array(
                    'alias' => 'dk_users.created_at',
                    'rule' => '%alias% > \'%s\'',
                ),
                'endTime' => array(
                    'alias' => 'dk_users.created_at',
                    'rule' => '%alias% < \'%s\'',
                ),
            ),
        );
        $parent_id = intval($request['parent']);
        $formatquery = new Formatquery($config);
        $query = $formatquery->setParams($request)->getParams();
        $types = config('my.site.recomm_types');
        $mysql = DB::table('users')->where('recomm_type', array_search('agents', $types))
            ->whereRaw(($parent_id && in_array($parent_id, $ids)) ? "`dk_users`.`recomm_id` = {$parent_id}" : 1)
            ->whereRaw($query['whereStr'] ? $query['whereStr'] : 1);
        $ret = [
            'total' => $mysql->count(),
            'rows' => [],
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
