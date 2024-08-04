<?php

namespace App\Http\Requests;

use App\Actions\AdminAuthorization;
use App\Services\FormRequestHandleInputs;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class categoriesFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'parent_id'=>'filled',
            'name'=>'required|string',
            'info'=>'nullable|string',
        ];
        return $arr;

    }
}
