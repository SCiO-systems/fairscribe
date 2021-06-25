<?php

namespace App\Http\Resources\v1;

use DB;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamCollectionResourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $metadata_record = DB::connection('mongodb')->table('metadata_records')
            ->where('_id', $this->external_metadata_record_id)
            ->first();

        return [
            'id' => $this->id,
            'version' => $this->version,
            'external_metadata_record_id' => $this->external_metadata_record_id,
            'metadata_record' => $metadata_record,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'pii_status' => $this->pii_status,
            'findable_score' => $this->findable_score,
            'accessible_score' => $this->accessible_score,
            'interoperable_score' => $this->interoperable_score,
            'reusable_score' => $this->reusable_score,
            'fair_scoring' => $this->fair_scoring,
            'published_at' => $this->published_at,
            'issued_at' => $this->issued_at,
            'author_id' => $this->author_id,
            'publisher_id' => $this->publisher_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
