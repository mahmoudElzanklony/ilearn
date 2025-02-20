<?php

namespace App\Http\Resources;

use App\Actions\v2\BaseUrlForVideoAction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoQualitiesResource extends JsonResource
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
          'file'=>BaseUrlForVideoAction::handle($this->wasbi_url),
          'quality'=>$this->quality == 'original' ? 'HD':$this->quality,
            'file_updated_at'=>$this->updated_at,
        ];
    }
}
