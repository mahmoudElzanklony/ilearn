<?php

namespace App\Jobs;

use App\Models\images;
use App\Models\subjects_videos;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateExpiringWasabiUrls implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->processVideos();
        $this->processImages();
    }

    private function processImages(): void
    {
        $page = 1;
        $perPage = 10;

        do {
            $images = images::query()->paginate($perPage, ['*'], 'page', $page);
            Log::info('start build new urls');
            foreach ($images as $image) {
                $expiration = Carbon::now()->addHours(11);
                $temporaryUrl = Storage::disk('wasabi')
                    ->temporaryUrl($image->name, $expiration);

                $image->wasbi_url = $temporaryUrl;
                $image->save();
            }

            $page++;
        } while ($images->hasMorePages());
    }

    private function processVideos(): void
    {
        $page = 1;
        $perPage = 10;

        do {
            $videos = subjects_videos::query()->with('qualities')->paginate($perPage, ['*'], 'page', $page);
            Log::info('start build new urls');
            foreach ($videos as $video) {
                $expiration = Carbon::now()->addHours(11);
                $filePath = 'videos/' . $video->video;

                if (Storage::disk('wasabi')->exists($filePath)) {
                    $temporaryUrl = Storage::disk('wasabi')
                        ->temporaryUrl($filePath, $expiration);

                    $video->wasbi_url = $temporaryUrl;
                    $video->save();
                }
                foreach ($video->qualities as $quality) {
                    $filePath = 'videos/' . $quality->name;
                    if (Storage::disk('wasabi')->exists($filePath)) {
                        $temporaryUrl = Storage::disk('wasabi')
                            ->temporaryUrl($filePath, $expiration);

                        $quality->wasbi_url = $temporaryUrl;
                        $quality->save();
                    }
                }
            }

            $page++;
        } while ($videos->hasMorePages());
    }


}
