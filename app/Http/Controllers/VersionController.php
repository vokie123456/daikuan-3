<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\VersionRepository;
use App\Http\Requests\StoreVersionPost;

class VersionController extends Controller
{
    protected $versionRepository;
    
    public function __construct(VersionRepository $versionRepository) {
        $this->versionRepository = $versionRepository;
    }

    public function getNowVersion() {
        $this->set_data('android', $this->versionRepository->getRowByType(0));
        $this->set_data('ios', $this->versionRepository->getRowByType(1));
        $this->set_success('获取成功!');
        return response()->json($this->get_result());
    }

    public function save(StoreVersionPost $request) {
        if($this->versionRepository->create($request->all())) {
            $this->set_success('存储成功');
        }else {
            $this->set_error('存储失败');
        }
        return response()->json($this->get_result());
    }
}
