<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EjaculationResource extends JsonResource
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
            'checked_in_at' => $this->ejaculated_date->format(\DateTime::ATOM),
            'note' => $this->note,
            'link' => $this->link,
            'tags' => $this->tags->pluck('name'),
            'source' => $this->source,
            'is_private' => $this->is_private,
            'is_too_sensitive' => $this->is_too_sensitive,
            'discard_elapsed_time' => $this->discard_elapsed_time,
        ];
    }
}
