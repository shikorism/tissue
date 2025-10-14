<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
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
            'user_id' => $this->user_id, // private
            'user_name' => $this->user->name, // private
            'title' => $this->title,
            'is_private' => $this->is_private,
            'updated_at' => $this->updated_at, // private
            'user' => new UserResource($this->whenLoaded('user')), // private
        ];
    }
}
