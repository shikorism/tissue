<?php

namespace App\Http\Resources;

use App\Like;
use Carbon\Carbon;
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
            'user' => new UserResource($this->user),
            'is_liked' => $this->whenHas('is_liked'),
            'likes' => $this->whenLoaded('likes', fn ($likes) => UserResource::collection($likes->pluck('user'))),
            'likes_count' => $this->whenHas('likes_count'),
            'checkin_interval' => $this->whenHas('checkin_interval', fn ($interval) => (int) $interval),
            'previous_checked_in_at' => $this->whenHas('previous_checked_in_at', fn ($date) => (new Carbon($date, config('app.timezone')))->format(\DateTime::ATOM)),
            'is_muted' => $this->whenHas('is_muted'),
        ];
    }
}
