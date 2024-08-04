<?php

namespace App\Http\Requests;

use App\Actions\AdminAuthorization;
use App\Services\FormRequestHandleInputs;
use Illuminate\Foundation\Http\FormRequest;

class propertiesDataFormRequest extends FormRequest
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
    public function rules(): array
    {
        $arr =  [
            'id'=>'filled',
            'property_id_heading'=>'required',
            'price'=>'required',
        ];
        $arr = FormRequestHandleInputs::handle($arr,['name']);
        return $arr;
    }
}
