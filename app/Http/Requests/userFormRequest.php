<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class userFormRequest extends FormRequest
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
        if(request()->filled('id')) {
            return [
                'username' => 'filled',
                'phone' => 'filled|unique:users,phone,'. auth()->id(),
                'password' => 'filled',
                'type' => 'filled',
                'ip'=>'nullable',
                'id'=>'required|exists:users,id'
            ];
        }else{
            return [
                'username' => 'required',
                'phone' => 'required|unique:users,phone',
                'password' => 'required',
                'type' => 'required',
            ];
        }
    }

    public function attributes()
    {
        return [
          'phone'=>__('keywords.phone')
        ];
    }


}
