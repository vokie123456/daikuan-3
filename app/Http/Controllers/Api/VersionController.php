<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\VersionRepository;

class VersionController extends Controller
{
    protected $versionRepository;
    
    public function __construct(VersionRepository $versionRepository) {
        $this->versionRepository = $versionRepository;
    }

    //获取当前版本
    public function getNowVersion() {
        $this->set_data('android', $this->formatData($this->versionRepository->getRowByType(0)));
        $ios_data = $this->formatData($this->versionRepository->getRowByType(1));
        if(isset($ios_data['url']) && strpos($ios_data['url'], 'itms-services://') !== 0) {
            $ios_data['url'] = 'itms-services://?action=download-manifest&url=' . $ios_data['url'];
        }
        $this->set_data('ios', $ios_data);
        $this->set_success('获取成功!');
        return response()->json($this->get_result());
    }

    public function formatData($data) {
        if($data) {
            return [
                'version' => $data['version'],
                'url' => $data['url'],
                'type' => $data['isForce'],
                'text' => $data['details'],
            ];
        }
        return $data;
    }
}
