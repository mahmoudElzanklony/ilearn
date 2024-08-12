<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
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
          'doctor'=>UserResource::make($this->whenLoaded('doctor')),
          'start_date'=>$this->start_date,
          'end_date'=>$this->end_date,
          'total_money'=>$this->total_money,
          'remain'=>$this->remain,
          'note'=>$this->note,
          'created_at'=>$this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
