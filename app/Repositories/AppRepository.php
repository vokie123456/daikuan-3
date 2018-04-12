<?php
namespace App\Repositories;

use App\Models\App as AppModel;
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
            'sortArr'   => array('created_at', 'name', 'status',  'company_name' => 'company_id'),
            'searchArr' => array(
                'name'  => ['rule' => '%alias% like \'%%s%\'',],
            ),
        );
        $formatquery = new Formatquery($config);
        $query = $formatquery->setParams($request)->getParams();
        // error_log(print_r($query, true));
        return $this->appRepository::with('company')->orderBy($query['sort'], $query['order'])
                ->whereRaw($query['whereStr'] ? $query['whereStr'] : 1)
                ->skip($query['offset'])
                ->take($query['limit'])
                ->get();
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
            'company_id' => (int)$datas['company_id'],
            'synopsis' => $datas['synopsis'] ? $datas['synopsis'] : '',
            'details' => $datas['details'] ? $datas['details'] : '',
            'rate' => (float)$datas['rates']['value'],
            'rate_type' => (int)$datas['rates']['type'],
            'moneys' => json_encode($moneys),
            'terms' => $datas['terms'],
            'repayments' => $datas['repayments'],
            'apply_number' => (int)$datas['apply_number'],
            'sort' => (int)$datas['sort'],
            'recommend' => intval(floatval($datas['recommend']) * 2),
            'status' => (isset($datas['status']) && $datas['status']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        );
        return $_data;
    }

    public function create($datas)
    {
        $_data = $this->format_data($datas);
        $_data['created_at'] = date('Y-m-d H:i:s');
        return $this->appRepository::create($_data);
    }

    public function update($datas)
    {
        $_data = $this->format_data($datas);
        $_data['icon'] = str_replace('/storage/', '', $_data['icon']);
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
        return $this->appRepository->where('id', $id)->delete();
    }
}