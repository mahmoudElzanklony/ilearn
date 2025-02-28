<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class subjectsVideoFormRequest extends FormRequest
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
            'user_id'=>'filled|exists:users,id',
            'subject_id'=>'required|exists:subjects,id',
            'video'=>'filled|mimes:mp4,pdf',
            'name'=>'required',
        ];
    }
}
