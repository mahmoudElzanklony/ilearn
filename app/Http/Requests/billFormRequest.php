<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class billFormRequest extends FormRequest
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
            'doctor_id'=>'required|exists:users,id',
            'start_date'=>'required|date',
            'end_date'=>'required|date',
            //'profit'=>'required|numeric',
            'remain'=>'required|numeric|min:0',
            'note'=>'nullable',
        ];
    }
}
