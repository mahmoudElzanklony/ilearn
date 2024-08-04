<?php

namespace App\Http\Requests;

use App\Http\Enum\SendingNotificationEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class notificationsScheduleFormRequest extends FormRequest
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
            'content'=>'required',
            'sending_type'=>['required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, array_column(SendingNotificationEnum::cases(), 'value'))) {
                        $fail("The $attribute field is not a valid value.");
                    }
                },
            ],
            'users'=>'required|array',
            'users.*'=>'required|exists:users,id',
        ];
    }
}
