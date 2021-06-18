<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\TeamResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InviteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'team' => new TeamResource($this->team),
        ];
    }
}
