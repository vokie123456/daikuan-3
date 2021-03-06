<?php

namespace App\Repositories;

use App\Models\Company;
use App\Services\Formatquery;

class CompanyRepository
{
    protected $company;
    
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    //获取公司列表
    public function getList($request = []) 
    {
        if(isset($request['all'])) {
            return $this->company::orderBy('created_at', 'desc')->get();
        }
        $config = array(
            'defSort'   => 'created_at',
            'defOrder'  => 'desc',
            'sortArr'   => array('created_at', 'name',),
            'searchArr' => array(
                'name'  => ['rule' => '%alias% like \'%%s%\'',],
            ),
        );
        $formatquery = new Formatquery($config);
        $query = $formatquery->setParams($request)->getParams();
        // error_log(print_r($query, true));
        return $this->company::orderBy($query['sort'], $query['order'])
                ->whereRaw($query['whereStr'] ? $query['whereStr'] : 1)
                ->skip($query['offset'])
                ->take($query['limit'])
                ->get();
    }

    //添加一个新公司
    public function addACompany($name)
    {
        $company = array(
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        return $this->company::create($company);
    }

    public function updateCompany($name, $id)
    {
        $company = array(
            'name' => $name,
            'updated_at' => date('Y-m-d H:i:s'),
        );
        return $this->company->where('id', $id)->update($company);
    }

    public function delCheck($id)
    {
        return \App\Models\App::where('company_id', $id)->first();
    }

    public function delCompany($id)
    {
        return $this->company->where('id', $id)->delete();
    }
}
