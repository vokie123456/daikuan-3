<?php
namespace App\Repositories;

use App\Models\App as AppModel;
use App\Models\CategoryApp;
use App\Services\Formatquery;

class AppRepository
{
    protected $appRepository;
    
    public function __construct(AppModel $app)
    {
        $this->appRepository = $app;
    }

    public function getApp($id)
    {
        return $this->appRepository->find($id);
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
                // 'company_name' => 'company_id',
            ),
            'searchArr' => array(
                'name'  => ['rule' => '%alias% like \'%%s%\'',],
            ),
        );
        $formatquery = new Formatquery($config);
        $query = $formatquery->setParams($request)->getParams();
        // error_log(print_r($query, true));
        $where = $query['whereStr'] ? $query['whereStr'] : 1;
        $ret['total'] = $this->appRepository->whereRaw($where)->count();
        if($ret['total']) {
            $ret['rows'] = $this->appRepository
                //->with('company')
                ->orderBy($query['sort'], $query['order'])
                ->whereRaw($where)
                ->skip($query['offset'])
                ->take($query['limit'])
                ->get();
        }
        return $ret;
    }

    public function format_data($datas)
    {
        $datas['rates'] = json_decode($datas['rates'], true);
        $moneys = json_decode($datas['moneys'], true);
        $moneys = array_map(function($val) {return floatval($val);}, $moneys);
        $_data = array(
            'name' => $datas['name'],
            'weburl' => $datas['weburl'],
            'icon' => $datas['appicon'],
            'note' => $datas['note'],
            // 'company_id' => (int)$datas['company_id'],
            'synopsis' => $datas['synopsis'] ? $datas['synopsis'] : '',
            'details' => $datas['details'] ? $datas['details'] : '',
            'rate' => (float)$datas['rates']['value'],
            'rate_type' => (int)$datas['rates']['type'],
            'moneys' => json_encode($moneys),
            'terms' => $datas['terms'],
            'marks' => $datas['marks'],
            'repayments' => $datas['repayments'],
            'apply_number' => (int)$datas['apply_number'],
            'sort' => (int)$datas['sort'],
            'recommend' => intval(floatval($datas['recommend']) * 2),
            'status' => (isset($datas['status']) && $datas['status']) ? 1 : 0,
            'isNew' => (isset($datas['isNew']) && $datas['isNew']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        );
        return $_data;
    }

    public function create($datas)
    {
        $_data = $this->format_data($datas);
        $_data['created_at'] = date('Y-m-d H:i:s');
        return $this->appRepository->create($_data);
    }

    public function update($datas)
    {
        $_data = $this->format_data($datas);
        $_data['icon'] = rm_path_prev_storage($_data['icon']);
        return $this->appRepository->where('id', $datas['id'])->update($_data);
    }

    public function updateStatus($id, $status)
    {
        return $this->appRepository
                ->where('id', $id)
                ->update(['status' => ($status ? 1 : 0)]);
    }

    public function destroy($id)
    {
        $ret = CategoryApp::where('app_id', $id)->delete();
        if($ret) $ret = $this->appRepository->where('id', $id)->delete();
        return $ret;
    }
}