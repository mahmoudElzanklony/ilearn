<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoViewResource extends JsonResource
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
          'video_id'=>$this->video_id,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'video'=>UserResource::make($this->whenLoaded('video')),
          'created_at'=>$this->created_at->format('Y-m-d H:i:s'),

        ];
    }
}
