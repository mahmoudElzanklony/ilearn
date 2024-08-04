<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'=>$this->id,
            'username'=>$this->username,
            'phone'=>$this->phone,
            'ip'=>$this->otp_secret,
            'type'=>$this->type,
            'created_at'=>$this->created_at->format('Y-m-d H:i:s')
        ];
        if(isset($this->token)){
            $data['token'] = $this->token;
        }
        return $data;
    }
}
