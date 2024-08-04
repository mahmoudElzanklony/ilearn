<?php

namespace App\Http\Requests;

use App\Actions\AdminAuthorization;

use App\Services\FormRequestHandleInputs;
use Illuminate\Foundation\Http\FormRequest;

class couponFormRequest extends FormRequest
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
            'serial'=>'required',
            'expiration_at'=>'required|date',
            'max_number_of_users'=>'required|integer',
            'max_usage_per_user'=>'required|integer',
            'type'=>'required',
            'value'=>'required|numeric',
            'max_value'=>'required|numeric',
        ];
        $arr = FormRequestHandleInputs::handle($arr,['name']);
        return $arr;

    }
}
