<?php

namespace App\Repositories;

use App\Models\Banner;
use App\Services\Formatquery;
use App\Models\App as AppModel;

class BannerRepository
{
    protected $banner;
    
    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
    }

    public function getList($request = []) 
    {
        $config = array(
            'defSort'   => 'created_at',
            'defOrder'  => 'desc',
            'sortArr'   => array(
                'created_at', 
                'name', 
                'status',
                'position',
                'start_time',
                'end_time',
                'sort',
            ),
            'searchArr' => array(
                'name'  => ['rule' => '%alias% like \'%%s%\'',],
                'stime' => [
                    'alias' => 'start_time',
                    'rule' => '%alias% >= \'%s\'',
                ],
                'etime' => [
                    'alias' => 'end_time',
                    'rule' => '%alias% <= \'%s\'',
                ],
                'show_time' => [
                    'rule' => '`start_time` <= \'%s\' AND `end_time` >= \'%s\'',
                ],
                'position' => true,
                'status' => true,
            ),
        );
        $formatquery = new Formatquery($config);
        $query = $formatquery->setParams($request)->getParams();
        // error_log(print_r($query, true));
        $ret = [
            'total' => 0,
            'rows' => [],
        ];
        $where = $query['whereStr'] ? $query['whereStr'] : 1;
        $ret['total'] = $this->banner->whereRaw($where)->count();
        if($ret['total']) {
            $ret['rows'] = $this->banner
                ->orderBy($query['sort'], $query['order'])
                ->whereRaw($where)
                ->skip($query['offset'])
                ->take($query['limit'])
                ->get();
        }
        return $ret;
    }

    public function getById($id)
    {
        return $this->banner->find($id);
    }

    public function checkAppId($id)
    {
        return (bool)AppModel::find($id);
    }

    public function format_data($datas)
    {
        $_data = array(
            'name' => $datas['name'],
            'position' => (int)$datas['position'],
            'type' => (int)$datas['type'],
            'app_id' => (int)$datas['app_id'] > 0 ? $datas['app_id'] : null,
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

    public function updateStatus($id, $status)
    {
        return $this->banner
                ->where('id', $id)
                ->update(['status' => ($status ? 1 : 0)]);
    }

    public function delete($id)
    {
        return $this->banner->where('id', $id)->delete();
    }
}