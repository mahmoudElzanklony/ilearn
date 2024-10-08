<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class subscriptionsFormRequest extends FormRequest
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
            'user_id'=>'required|exists:users,id',
            'subject_id'=>'required',
            'subject_id.*'=>'filled|exists:subjects,id',
            'discount'=>'filled',
            'note'=>'nullable'
        ];
    }
}
