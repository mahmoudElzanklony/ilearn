<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionsResource extends JsonResource
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
          'user_id'=>$this->user_id,
          'subject_id'=>$this->subject_id,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'subject'=>SubjectsResource::make($this->whenLoaded('subject')),
          'price'=>$this->price,
          'discount'=>$this->discount,
          'note'=>$this->note,
          'created_at'=>$this->created_at->format('Y-m-d H:i:s'),

        ];
    }
}
