<?php

namespace App\Http\Resources;

use App\Services\StreamImages;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function tem_url($path)
    {
        $expires = now()->addMinutes(60);

        $url = Storage::disk('wasabi')
            ->temporaryUrl($path, $expires, [
            'ResponseContentType' => 'application/octet-stream',
            // Additional headers or options can be added here
        ]);
        return $url;
    }
    public function toArray(Request $request): array
    {
        return [
            //'id'=>$this->id,
            //'imageable_type'=>$this->imageable_type,
            //'name'=>env('cloud_storage').(env('WAS_STATUS') == 1 ? '/' :'/images/').($this->name ?? 'default.png'),
            'name'=>$this->wasbi_url ,
            //'name'=>$this->tem_url($this->name)
        ];
    }
}
