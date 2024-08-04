<?php

namespace App\Http\Requests;

use App\Actions\AdminAuthorization;
use App\Services\FormRequestHandleInputs;
use Illuminate\Foundation\Http\FormRequest;

class servicesFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return AdminAuthorization::check();
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules():array
    {
        $arr = [
            'id'=>'filled',
            'category_id'=>'required|exists:categories,id',
            'image'=>'filled|image|mimes:png,jpg,jpeg,gif,svg',
            'price'=>'required',
        ];
        $arr = FormRequestHandleInputs::handle($arr,['info:filled','name']);
        return $arr;

    }

    public function attributes()
    {
        return [
          'ar_name'=>__('keywords.ar_name'),
          'en_name'=>__('keywords.en_name'),
          'price'=>__('keywords.price'),
          'category_id'=>__('keywords.category'),
        ];
    }
}
