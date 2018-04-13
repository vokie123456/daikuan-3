<?php

namespace App\Repositories;

use App\Models\Banner;
use Illuminate\Support\Facades\DB;

class BannerRepository
{
    protected $banner;
    
    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
    }

    public function getById($id)
    {
        return $this->banner->find($id);
    }

    public function checkAppId($id)
    {
        return (bool)DB::table('apps')->find($id);
    }

    public function format_data($datas)
    {
        $_data = array(
            'name' => $datas['name'],
            'position' => (int)$datas['position'],
            'type' => (int)$datas['type'],
            'app_id' => (int)$datas['app_id'],
            'url' => isset($datas['url']) ? (string)$datas['url'] : '',
            'image' => isset($datas['image']) ? (string)$datas['image'] : '',
            'start_time' => isset($datas['start_time']) ? $datas['start_time'] : null,
            'end_time' => isset($datas['end_time']) ? $datas['end_time'] : null,
            'sort' => (int)$datas['sort'],
            'status' => (isset($datas['status']) && $datas['status']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        );
        return $_data;
    }

    public function create($datas)
    {
        $_data = $this->format_data($datas);
        $_data['created_at'] = date('Y-m-d H:i:s');
        return $this->banner::create($_data);
    }

    public function update($datas)
    {
        $_data = $this->format_data($datas);
        $_data['image'] = rm_path_prev_storage($_data['image']);
        return $this->banner->where('id', $datas['id'])->update($_data);
    }
}