<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SettingRepository;

class ContactUsController extends Controller
{
    //
    protected $settingRepository;
    protected $set_type = 'app';
    protected $set_code = 'app_contactus';
    
    public function __construct(SettingRepository $settingRepository) {
        $this->settingRepository = $settingRepository;
    }

    public function getInfo() {
        $data = $this->settingRepository->getSettings($this->set_type);
        $content = isset($data[$this->set_code]) ? $data[$this->set_code] : '';
        $this->set_success('获取成功!')->set_data('data', $content);
        return response()->json($this->get_result());
    }

    public function setInfo(Request $request) {
        $this->settingRepository->setSettings([
            $this->set_code => $request->input('content'),
        ], $this->set_type);
        return response()->json($this->set_success('更新成功!')->get_result());
    }
}
