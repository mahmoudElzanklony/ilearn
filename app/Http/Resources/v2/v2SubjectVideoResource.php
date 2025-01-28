<?php

namespace App\Http\Resources\v2;

use App\Http\Resources\ImageResource;
use App\Http\Resources\SubjectsResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VideoQualitiesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class v2SubjectVideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $arr =  [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'subject_id'=>$this->subject_id,
            'user'=>UserResource::make($this->whenLoaded('user')),
            'subject'=>SubjectsResource::make($this->whenLoaded('subject')),
            'image'=>ImageResource::make($this->whenLoaded('image')),
            'qualities'=>VideoQualitiesResource::collection($this->whenLoaded('qualities')),
            'file'=>$this->wasbi_url,
            'type'=>$this->type,
            'extension'=>pathinfo($this->video, PATHINFO_EXTENSION),
            'name'=>$this->name,
            'render'=>'url', // or stream
            'created_at'=>$this->created_at->format('Y-m-d'),
            'updated_at'=>$this->created_at
        ];
        return $arr;
    }
}
