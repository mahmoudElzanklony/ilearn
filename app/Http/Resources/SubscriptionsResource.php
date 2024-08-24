<?php

namespace App\Http\Resources;

use App\Models\subjects_videos;
use App\Models\users_videos_views;
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
        $video_base_query = subjects_videos::query();
        $user_view_base_query = users_videos_views::query();
        $arr =  [
          'id'=>$this->id,
          'user_id'=>$this->user_id,
          'username'=>$this->subject->user->username ?? $this->user->username,
          'subject_id'=>$this->subject_id,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'subject'=>SubjectsResource::make($this->whenLoaded('subject')),
          'price'=>$this->price,
          'discount'=>$this->discount,
          'note'=>$this->note,

          'videos'=>SubjectsVideosResource::collection($this->whenLoaded('videos')),
          'created_at'=>$this->created_at->format('Y-m-d H:i:s'),

        ];
        if(request()->filled('web')){
          $arr['total_videos_per_subject']=$video_base_query->clone()->where('subject_id','=',$this->subject_id)->count();
          $arr['total_seen_per_user']=$user_view_base_query->clone()
                ->whereHas('video',fn($e)=> $e->where('subject_id','=',$this->subject_id))
                ->where('user_id','=',$this->user_id)
                ->count();
        }
        return $arr;
    }
}
