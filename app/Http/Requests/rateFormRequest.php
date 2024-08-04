<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class rateFormRequest extends FormRequest
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
            'order_id'=>'required|exists:orders,id',
            'print_rate'=>'required|numeric|max:5',
            'delivery_rate'=>'required|numeric|max:5',
            'comment'=>'required'
        ];
    }

    public function attributes()
    {
        return [
          'print_rate'=>__('keywords.print_rate'),
          'delivery_rate'=>__('keywords.delivery_rate'),
          'comment'=>__('keywords.comment'),
        ];
    }
}
