<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class StoreCategoryPost extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:45',
            'type' => 'required|in:0,1,2,3',
            'sort_app' => 'in:0,1,2,3',
        ];
        if(!empty(request('type')) && request('type') == 1) {
            $rules['image'] = 'bail|required|image|max:200';
            if(!empty(request('image')) && is_string(request('image'))) {
                $img = rm_path_prev_storage(request('image'));
                if(Storage::disk('public')->exists($img)) {
                    unset($rules['image']);
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => '类别名称',
            'type' => '显示位置',
            'image' => '类别图片',
            'sort' => '类别排序',
            'sort_app' => '类别内app的排序',
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
