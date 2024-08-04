<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ordersFormRequest extends FormRequest
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
            'city'=>'required',
            'region'=>'required',
            'street'=>'required',
            'address'=>'required',
            'house_number'=>'required',
            'coordinates'=>'required',
            'note'=>'nullable',
            'payment.type'=>'required',
            'coupon_serial'=>'filled',
            'items'=>'required|array',
            'items.*.service_id'=>'required|exists:services,id',
            'items.*.file'=>'required|file|mimes:pdf,docx,pptx',
            'items.*.paper_number'=>'required|numeric',
            'items.*.copies_number'=>'required|numeric',
            'items.*.properties'=>'required|array',
            'items.*.properties.*.property_id'=>'required|exists:properties,id',
        ];
    }

    public function attributes()
    {
        return [
            'city'=>__('keywords.city'),
            'region'=>__('keywords.region'),
            'street'=>__('keywords.street'),
            'house_number'=>__('keywords.house_number'),
            'coordinates'=>__('keywords.coordinates'),
            'items.*.service_id'=>__('keywords.service'),
            'items.*.file'=>__('keywords.file'),
            'items.*.paper_number'=>__('keywords.paper_number'),
            'items.*.copies_number'=>__('keywords.copies_number'),
            'items.*.properties.*.property_id'=>__('keywords.property'),
        ];
    }
}
