<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectsVideosResource extends JsonResource
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
          'video'=>'videos/'.$this->video,
          'name'=>$this->name,
          'created_at'=>$this->created_at->format('Y-h-m ')
        ];
    }
}
