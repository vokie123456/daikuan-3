<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Repositories\CompanyRepository;

class CompanyController extends Controller
{
    protected $company;

    public function __construct(CompanyRepository $company)
    {
        $this->company = $company;
    }

    //获取公司列表
    public function index(Request $request)
    {
        $datas = CompanyResource::collection($this->company->getList($request->all()));
        $this->set_success('获取成功')->set_data('companies', $datas);
        return response()->json($this->get_result());
    }

    //添加一个新公司
    public function create(Request $request)
    {
        $validator = $this->validaData($request->all());
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
    public function update(Request $request)
    {
        $id = $request->get('id');
        if(!$id) {
            $this->set_error('缺少参数');
        }else {
            $validator = $this->validaData($request->all());
            if($validator->fails()) {
                $this->set_error($validator->errors()->first());
            }else {
                $result = $this->company->updateCompany($request->get('name'), $id);
                if($result) {
                    $this->set_success('更新成功')->set_data('company', $result);
                }else {
                    $this->set_error('更新失败');
                }
            }
        }
        return response()->json($this->get_result());
    }

    //删除公司
    public function delete($id)
    {
        if(!$id) {
            $this->set_error('缺少参数');
        }else if($this->company->delCheck($id)) {
            $this->set_error('该公司下有关联APP, 无法删除!');
        }else {
            $result = $this->company->delCompany($id);
            if($result) {
                $this->set_success('删除成功')->set_data('result', $result);
            }else {
                $this->set_error('删除失败');
            }
        }
        return response()->json($this->get_result());
    }

    //验证公司数据
    protected function validaData($datas)
    {
        return Validator::make($datas, [
            'name' => 'required|max:45',
        ], [
            'name.required' => '公司名称不能为空!',
        ]);
    }
}
