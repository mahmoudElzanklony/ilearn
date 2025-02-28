<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class subjectsFormRequest extends FormRequest
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
            'id'=>'filled',
            'name'=>'required',
            'category_id'=>'required|exists:categories,id',
            'semester'=>'required',
            'support_bluetooth'=>'required',
            'price'=>'required',
            'note'=>'required'
        ];
    }
}
