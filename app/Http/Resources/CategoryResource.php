<?php

namespace App\Http\Resources;

use App\Models\languages;
use App\Services\FormRequestHandleInputs;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
             'name'=>$this->name,
             'info'=>$this->info,
            'created_at'=>$this->created_at->format('Y-m-d H:i:s'),
        ];

    }
}
