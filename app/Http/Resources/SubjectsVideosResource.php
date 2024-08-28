<?php

namespace App\Http\Resources;

use App\Services\StreamImages;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
          'image'=>ImageResource::make($this->whenLoaded('image')),

          //'video'=>env('cloud_storage').(env('WAS_STATUS') == 1 ? '/':'/videos/').$this->video,
         // 'video'=>StreamImages::stream('videos/'.$this->video),
          'video'=>StreamImages::stream('videos/'.$this->video),
          'extension'=>pathinfo($this->video, PATHINFO_EXTENSION),
          'name'=>$this->name,
            'created_at'=>$this->created_at->format('Y-m-d'),
          'updated_at'=>$this->updated_at
        ];
    }
}
