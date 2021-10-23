<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

class TeamResourceFileResource extends JsonResource
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
            'path' => $this->path,
            'filename' => $this->filename,
            'pii_check_status' => $this->pii_check_status,
            'url' => Storage::url($this->path),
        ];
    }
}
