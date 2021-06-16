<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'role' => $this->role,
            'profile_picture_url' => $this->profile_picture_url,
            'ui_language' => $this->ui_language,
            'ui_language_display_format' => $this->ui_language_display_format,
            'ui_date_display_format' => $this->ui_date_display_format,
        ];
    }
}
