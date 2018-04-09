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
            'sortArr'   => array('created_at', 'name', 'company_name' => 'company_id'),
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
        $_data = array(
            'name' => $datas['name'],
            'weburl' => $datas['weburl'],
            'icon' => isset($datas['icon']) ? $datas['icon'] : $datas['appicon'],
            'company_id' => (int)$datas['company_id'],
            'synopsis' => $datas['synopsis'] ? $datas['synopsis'] : '',
            'details' => $datas['details'] ? $datas['details'] : '',
            'rate' => (int)$datas['rates']['value'],
            'rate_type' => (int)$datas['rates']['type'],
            'moneys' => $datas['moneys'],
            'terms' => $datas['terms'],
            'repayments' => $datas['repayments'],
            'apply_number' => (int)$datas['apply_number'],
            'recommend' => intval(floatval($datas['recommend']) * 2),
            'status' => $datas['terms'] ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        return $_data;
    }

    public function create($datas)
    {
        return $this->appRepository::create($this->format_data($datas));
    }

    public function update($datas)
    {
        $data = $this->format_data($datas);
        $data['icon'] = str_replace(['storage/', '/storage/'], '', $data['icon']);
        return $this->appRepository->where('id', $datas['id'])->update($data);
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