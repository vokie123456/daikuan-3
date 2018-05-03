<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VersionController extends Controller
{
    //获取当前版本
    public function getNowVersion() {
        $result = array(
            'error' => '',
            'errno' => 0,
            'datas' => array(
                'version' => 1,
                'url' => '#',
                'type' => 0, // 0 普通更新, 1 强制更新
                'text' => [
                    '1. 修复已知Bug', 
                    '2. 优化UI样式',
                ],
            ),
        );
        return response()->json($result);
    }
}
