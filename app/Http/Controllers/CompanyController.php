<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Repositories\CompanyRepository;

class CompanyController extends Controller
{
    public function __construct(CompanyRepository $company)
    {
        $this->company = $company;
    }

    //获取公司列表
    public function getAppCompanies(Request $request)
    {
        // DB::enableQueryLog();
        $datas = CompanyResource::collection($this->company->getList($request->all()));
        // error_log(print_r(DB::getQueryLog(), true));
        $this->set_success('获取成功')->set_data('companies', $datas);
        return response()->json($this->get_result());
    }

    //添加一个新公司
    public function addAppCompany(Request $request)
    {
        $validator = $this->validaCompanyData($request->all());
        if($validator->fails()) {
            $this->set_error($validator->errors()->first('name'));
        }else {
            $result = $this->company->addACompany($request->get('name'));
            if($result) {
                $this->set_success('添加成功')->set_data('company', $result);
            }else {
                $this->set_error('添加失败');
            }
        }
        return response()->json($this->get_result());
    }

    //更新公司数据
    public function updateCompany(Request $request)
    {
        $validator = $this->validaCompanyData($request->all(), ['id' => 'required|numeric']);
        if($validator->fails()) {
            $this->set_error($validator->errors()->first());
        }else {
            $result = $this->company->updateCompany($request->get('name'), $request->get('id'));
            error_log(print_r($result, true));
            if($result) {
                $this->set_success('更新成功')->set_data('company', $result);
            }else {
                $this->set_error('更新失败');
            }
        }
        return response()->json($this->get_result());
    }

    //验证公司数据
    protected function validaCompanyData($datas, $_rules = [])
    {
        $rules = [
            'name' => 'required|max:45',
        ];
        return Validator::make($datas, array_merge($rules, $_rules, [
            'id.required' => '缺少参数',
        ]));
    }
}
