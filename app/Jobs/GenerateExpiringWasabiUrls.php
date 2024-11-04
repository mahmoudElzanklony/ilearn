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
        dd('start of generation');
        // Assuming your images are stored with Wasabi storage
        $images = images::query()->get();
        $videos = subjects_videos::query()->get();

        foreach ($images as $image) {
            // Generate a presigned URL with a 12-hour expiration
            $expiration = Carbon::now()->addHours(11);
            $temporaryUrl = Storage::disk('wasabi')
                ->temporaryUrl($image->name, $expiration);

            // Update the wasbi_url column in the database
            $image->wasbi_url = $temporaryUrl;
            dd($image);
            $image->save(); // Use save instead of update


        }

        foreach ($videos as $video) {
            // Generate a presigned URL with a 12-hour expiration
            $expiration = Carbon::now()->addHours(11);
            $filePath = 'videos/' . $video->video;
            if (Storage::disk('wasabi')->exists($filePath)) {
                $temporaryUrl = Storage::disk('wasabi')
                    ->temporaryUrl($filePath, $expiration); // Assuming `path` is the column for the file path

                // Update the wasbi_url column in the database
                $video->wasbi_url = $temporaryUrl;
                $video->save(); // Use save instead of update
            }



        }
    }
}
