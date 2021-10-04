<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

class TeamResourceThumbnailResource extends JsonResource
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
            'extension' => $this->extension,
            'mime_type' => $this->mimetype,
            'pii_check' => $this->pii_check,
            'url' => Storage::temporaryUrl($this->path, now()->addHours(24)),
        ];
    }
}
