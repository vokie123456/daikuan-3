<?php
namespace App\Repositories;

use App\Models\Version;
use Illuminate\Support\Facades\Storage;

class VersionRepository
{
    protected $version;
    
    public function __construct(Version $version) {
        $this->version = $version;
    }

    public function getRowByType($type) {
        return $this->version->where('type', $type)->orderBy('id', 'desc')->first();
    }

    public function create($datas) {
        if(empty($datas['plist'])) $datas['plist'] = '';
        $ret = $this->version->create($datas);
        if($ret) {
            $filename = $datas['type'] == 1 ? 'ios.txt' : 'android.txt';
            Storage::disk('local')->put($filename, json_encode($datas));
        }
        return $ret;
    }
}
