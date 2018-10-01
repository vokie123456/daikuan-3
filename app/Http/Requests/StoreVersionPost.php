<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\ApiResponse;

class StoreVersionPost extends FormRequest
{
    use ApiResponse;

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
            //
            'type' => 'required|in:0,1',
            'version' => 'required|integer|min:1',
            'url' => 'required|string|max:255',
            'details' => 'required|string',
            'isForce' => 'required|in:0,1',
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
            'type' => '手机系统',
            'version' => '版本号',
            'url' => '下载地址',
            'details' => '更新说明',
            'isForce' => '是否强制更新',
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
        $this->set_error($validator->errors()->first());
        throw new ValidationException($validator, response()->json($this->get_result()));
    }
}
