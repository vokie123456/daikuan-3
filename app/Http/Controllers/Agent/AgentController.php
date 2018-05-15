<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AgentRepository;
use App\Http\Requests\StoreAgentPost;

class AgentController extends Controller
{
    protected $agent;
    protected $agentRepository;
    
    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }

    //
    public function myurl(Request $request)
    {
        $this->agent = session('agent');
        if($this->agent) {
            $share_url = config('my.site.register_path');
            $recom_key = config('my.site.recomm');
            $code = create_url_encode_by_id('agents', $this->agent->id);
            return view('agent/myurl', [
                'myname' => $this->agent->name,
                'myurl' => $share_url . "?{$recom_key}=" . $code,
                'headjs' => [ asset('js/qrcode.min.js') ],
            ]);
        }else {
            return redirect(route('showAgentLogin'));
        }
    }

    public function teams()
    {
        $this->agent = session('agent');
        if($this->agent) {
            return view('agent/team', [
                'stylesheets' => [
                    asset('bootstrap-table/bootstrap-table.css'),
                ],
                'headjs' => [
                    asset('bootstrap-table/bootstrap-table.js'),
                    asset('bootstrap-table/bootstrap-table-zh-CN.js'),
                    asset('js/utils.js'),
                ],
            ]);
        }else {
            return redirect(route('showAgentLogin'));
        }
    }

    public function teamdata(Request $request)
    {
        $this->agent = session('agent');
        if($this->agent) {
            $datas = $this->agentRepository->getList($request->all(), $this->agent->id);
            return response()->json($datas);
        }else {
            return response('你还未登录', 403);
        }
    }

    public function create()
    {
        $this->agent = session('agent');
        if($this->agent) {
            return view('agent/create');
        }else {
            return redirect(route('showAgentLogin'));
        }
    }

    public function createForm(StoreAgentPost $request)
    {
        $this->agent = session('agent');
        if($this->agent) {
            $datas = $request->all();
            $datas['parent_id'] = $this->agent->id;
            $ret = $this->agentRepository->create($datas);
            if($ret->is_success()) {
                return redirect('agents/teams');
            }else {
                exit($ret->get_error());
            }
        }else {
            return redirect(route('showAgentLogin'));
        }
    }

    public function promotes(Request $request)
    {
        $this->agent = session('agent');
        if($this->agent) {
            return view('agent/promote', [
                'childs' => $this->agentRepository->getAgentByParentId($this->agent->id),
                'stylesheets' => [
                    asset('bootstrap-table/bootstrap-table.css'),
                ],
                'headjs' => [
                    asset('bootstrap-table/bootstrap-table.js'),
                    asset('bootstrap-table/bootstrap-table-zh-CN.js'),
                    asset('laydate/laydate.js'),
                ],
                'javascripts' => [
                    asset('js/utils.js'),
                    asset('js/promote.js'),
                ],
            ]);
        }else {
            return redirect(route('showAgentLogin'));
        }
    }

    public function getPromoteDatas(Request $request)
    {
        $this->agent = session('agent');
        if($this->agent) {
            $datas = $this->agentRepository->getUserByAgent($this->agent->id, $request->all());
            return response()->json($datas);
        }else {
            return response('你还未登录', 403);
        }
    }
}
