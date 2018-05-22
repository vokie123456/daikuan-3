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
            'ios' => array(
                'version' => 5,
                'url' => 'itms-services://?action=download-manifest&url=https://app.wangtougongshe.com/apps/kuaihang.plist',
                'type' => 1, // 0 普通更新, 1 强制更新
                'text' => '发现新版本, 请更新.',
            ),
            'android' => array(
                'version' => 4,
                'url' => 'http://app.wangtougongshe.com/apps/android_app_v4.apk',
                'type' => 1, // 0 普通更新, 1 强制更新
                'text' => '发现新版本, 请更新.',
            ),
        );
        return response()->json($result);
    }
}
