<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class activationAccountFormRequest extends FormRequest
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
            'otp_secret'=>'required|digits:4',
            'phone'=>'filled',
            'email'=>'filled',
        ];
    }

    public function attributes()
    {
        return [
          'otp_secret'=>__('keywords.otp_secret'),
          'phone'=>__('keywords.phone'),
        ];
    }
}
