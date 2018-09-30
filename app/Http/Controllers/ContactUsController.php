<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UsRepository;

class ContactUsController extends Controller
{
    //
    protected $usRepository;
    
    public function __construct(UsRepository $usRepository) {
        $this->usRepository = $usRepository;
    }

    public function getInfo() {
        $this->set_success('获取成功!')->set_data('data', $this->usRepository->getData());
        return response()->json($this->get_result());
    }

    public function setInfo(Request $request) {
        $this->usRepository->setData($request->input('content'));
        return response()->json($this->set_success('更新成功!')->get_result());
    }
}
