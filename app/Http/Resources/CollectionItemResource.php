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
            'collection' => new CollectionResource($this->collection), // private
            'user_id' => $this->collection->user_id, // private, deprecated
            'user_name' => $this->collection->user->name, // private, deprecated
            'link' => $this->link,
            'note' => $this->note,
            'tags' => $this->tags->pluck('name'),
        ];
    }
}
