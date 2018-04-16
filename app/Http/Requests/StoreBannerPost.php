<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class StoreBannerPost extends FormRequest
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
        $image_rule = 'image|max:300';
        if(!empty(request('image')) && is_string(request('image'))) {
            $img = rm_path_prev_storage(request('image'));
            if(Storage::disk('public')->exists($img)) {
                $image_rule = 'max:255';
            }
        }
        return [
            'name' => 'required|string|max:45',
            'position' => 'required|in:0,1',
            'type' => 'required|in:0,1',
            'app_id' => 'nullable|integer|min:0',
            'url' => 'required_if:type,1|nullable|url|max:255',
            'image' => $image_rule,
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'sort' => 'integer|min:0',
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
            'name' => '广告名称',
            'position' => '显示位置',
            'type' => '跳转方式',
            'url' => '页面地址',
            'image' => '广告图片',
            'start_time' => '起始时间',
            'end_time' => '结束时间',
            'sort' => '排序序号',
        ];
    }

    /**
     * 定义验证规则的错误消息
     *
     * @return array
     * @translator laravelacademy.org
     */
    public function messages()
    {
        return [
            'url.required_if' => '广告跳至web页面,请填写url地址.',
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
