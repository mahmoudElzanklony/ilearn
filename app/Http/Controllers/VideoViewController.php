<?php

namespace App\Http\Controllers;

use App\Filters\EndDateFilter;
use App\Filters\StartDateFilter;
use App\Filters\SubjectIdFilter;
use App\Filters\UserIdFilter;
use App\Filters\VideoIdFilter;
use App\Http\Resources\SubscriptionsResource;
use App\Http\Resources\VideoViewResource;
use App\Models\subjects_videos;
use App\Models\subscriptions;
use App\Models\users_videos_views;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class VideoViewController extends Controller
{
    //
    public function seen()
    {
        users_videos_views::query()->updateOrCreate([
            'user_id'=>auth()->id(),
            'video_id'=>request('video_id')
        ],[
            'user_id'=>auth()->id(),
            'video_id'=>request('video_id')
        ]);
        return Messages::success(__('messages.saved_successfully'));
    }

    public function statics()
    {
        $data = users_videos_views::query()
            ->with(['video','user'])
            ->orderBy('id','DESC');
        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                UserIdFilter::class,
                VideoIdFilter::class
            ])
            ->thenReturn()
            ->paginate(request('limit') ?? 10);
        return VideoViewResource::collection($output);
    }
}
