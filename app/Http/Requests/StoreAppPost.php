<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreAppPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:45',
            'weburl' => 'required|url|max:255',
            'appicon' => 'bail|required|image|dimensions:ratio=1|max:200',
            'company_id' => 'required|integer|min:1',
            'rates' => [
                'required',
                'json',
                function($attribute, $value, $fail) {
                    $data = json_decode($value, true);
                    $rate = isset($data['value']) ? floatval($data['value']) : 0;
                    $types = [0, 1, 2, 3,];
                    if(!isset($data['value'])) {
                        return $fail(':attribute的数值不能为空!');
                    }else if(!isset($data['type'])) {
                        return $fail(':attribute的单位不能为空!');
                    }else if($rate <= 0 || $rate >= 100) {
                        return $fail(':attribute数值需在0-100以内!!');
                    }else if(!in_array(intval($data['type']), $types)) {
                        return $fail(':attribute的单位不存在!');
                    }
                }
            ],
            'moneys' => [
                'required',
                function ($attribute, $value, $fail) {
                    $data = json_decode($value, true);
                    if(!$data || count($data) == 0) {
                        return $fail(':attribute的数据不能为空!');
                    }
                },
            ],
            'terms' => [
                'required',
                function($attribute, $value, $fail) {
                    $datas = json_decode($value, true);
                    if(!$datas || count($datas) == 0) {
                        return $fail(':attribute 没有数据!');
                    }else {
                        $data = $datas[0];
                        $rate = isset($data['value']) ? $data['value'] : '';
                        $types = ['天', '周', '月', '年',];
                        if(!isset($data['value'])) {
                            return $fail(':attribute的数值不能为空!');
                        }else if(!isset($data['type'])) {
                            return $fail(':attribute的单位不能为空!');
                        }else if(!in_array(intval($data['type']), $types)) {
                            return $fail(':attribute的单位不存在!');
                        }
                    }
                }
            ],
            'repayments' => [
                'required',
                function($attribute, $value, $fail) {
                    $data = json_decode($value, true);
                    if(!$data || count($data) == 0) {
                        return $fail(':attribute的数据不能为空!');
                    }
                },
            ],
            'apply_number' => 'integer|min:0',
            'recommend' => 'required|numeric|min:0|max:5',
            'status' => 'required|boolean',
        ];
    }
    
    /**
     * 获取被定义验证规则的错误消息
     *
     * @return array
     * @translator laravelacademy.org
     */
    public function messages()
    {
        return [
            'company_id.integer' => ':attribute 无效',
            'company_id.min' => ':attribute 无效',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => '名称',
            'weburl' => '推广地址',
            'appicon' => '图标',
            'company_id' => '归属公司',
            'synopsis' => '简介',
            'details' => '详情',
            'rates' => '利率',
            'moneys' => '贷款金额',
            'terms' => '还款期限',
            'repayments' => '还款方式',
            'apply_number' => '申请人数',
            'recommend' => '推荐指数',
            'status' => '当前状态',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()->toArray(),
        ]));
    }
}
