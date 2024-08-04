<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class newPasswordFormRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            //
            'phone'=>'filled',
            'email'=>'filled',
            'password'=>'required|confirmed|min:6'
        ];
    }

    public function attributes()
    {
        return [
            'password'=>__('keywords.password')
        ];
    }
}
