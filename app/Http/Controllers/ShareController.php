<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SettingRepository;

class ShareController extends Controller
{
    protected $settingRepository;
    protected $set_type = 'share';
    
    public function __construct(SettingRepository $settingRepository) {
        $this->settingRepository = $settingRepository;
    }

    public function getInfo() {
        $data = $this->settingRepository->getSettings($this->set_type);
        $this->set_success('获取成功!')->set_data('data', $data);
        return response()->json($this->get_result());
    }

    public function setInfo(Request $request) {
        $wx_title = $request->input('wx_title');
        $wx_content = $request->input('wx_content');
        if(empty($wx_title)) {
            $this->set_error('标题不能为空!');
        }else if(empty($wx_content)) {
            $this->set_error('内容不能为空!');
        }else {
            $this->settingRepository->setSettings([
                'wx_title' => $wx_title,
                'wx_content' => $wx_content,
            ], $this->set_type);
            $this->set_success('更新成功!');
        }
        return response()->json($this->get_result());
    }
}
