<?php

namespace App\Http\Resources;

use App\Http\Resources\v2\v2SubjectVideoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectsResource extends JsonResource
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
          'category_id'=>$this->category_id,
          'user_id'=>$this->user_id,
          'category'=>CategoryResource::make($this->whenLoaded('category')),
          'image'=>ImageResource::make($this->whenLoaded('image')),
          //'image'=>['name'=>'https://ilearn.boodaidictionary.com/images/logo-sm.png'],
          'user'=>UserResource::make($this->whenLoaded('user')),
          'semester'=>$this->semester,
          'price'=>$this->price,
          'note'=>$this->note,
          'support_bluetooth'=>$this->support_bluetooth,
          'videos'=>v2SubjectVideoResource::collection($this->whenLoaded('videos')),
          'created_at'=>$this->created_at->format('Y-m-d H:i:s'),

        ];
    }
}
