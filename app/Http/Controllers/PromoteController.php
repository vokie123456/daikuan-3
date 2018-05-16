<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Api\PromoteRepository;

class PromoteController extends Controller
{
    protected $promoteRepository;
    
    public function __construct(PromoteRepository $promoteRepository)
    {
        $this->promoteRepository = $promoteRepository;
    }

    public function index(Request $request)
    {
        $datas = $this->promoteRepository->getList($request->all());
        $this->set_success('获取成功')->set_data('agents', $datas);
        return response()->json($this->get_result());
    }
}
