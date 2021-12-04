<?php

namespace App\Http\Resources;

use App\Facades\Formatter;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionItemResource extends JsonResource
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
            'collection_id' => $this->collection_id,
            'user_id' => $this->collection->user_id,
            'user_name' => $this->collection->user->name,
            'link' => $this->link,
            'note' => $this->note,
            'tags' => $this->tags->pluck('name'),
            // for internal use
            'note_html' => Formatter::linkify(nl2br(e($this->note))),
            'checkin_url' => $this->makeCheckinURL(),
        ];
    }
}
