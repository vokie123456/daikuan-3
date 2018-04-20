<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\DeviceRepository;

class DeviceController extends Controller
{
    //
    protected $deviceRepository;
    
    public function __construct(DeviceRepository $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    public function save(Request $request)
    {
        $ret = $this->deviceRepository->saveDeviceInfo($request->all());
        if($ret) {
            $this->set_success('保存成功!');
        }else {
            $this->set_error('保存失败!');
        }
        return response()->json($this->get_result());
    }
}
