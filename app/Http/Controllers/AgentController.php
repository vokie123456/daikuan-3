<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AgentRepository;
use App\Http\Requests\StoreAgentPost;

class AgentController extends Controller
{
    protected $agentRepository;
    
    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }

    public function index(Request $request)
    {
        $datas = $this->agentRepository->getList($request->all());
        $this->set_success('获取成功')->set_data('agents', $datas);
        return response()->json($this->get_result());
    }

    //
    public function getAll(Request $request)
    {
        $this->set_success('获取成功')->set_data('data', $this->agentRepository->getAll($request->all()));
        return response()->json($this->get_result());
    }

    public function store(StoreAgentPost $request)
    {
        $datas = $request->all();
        $ret = $this->agentRepository->create($datas);
        return response()->json($ret->get_result());
    }
}
