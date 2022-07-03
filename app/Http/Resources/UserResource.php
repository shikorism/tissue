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
            'id' => $this->id, // TODO: 公開するか悩ましい
            'name' => $this->name,
            'display_name' => $this->display_name,
            'is_protected' => $this->is_protected,
            'private_likes' => $this->is_protected || $this->private_likes, // 鍵垢の場合はフラグを秘匿
            $this->mergeWhen($this->isMe() || !$this->is_protected, [
                'bio' => $this->bio,
                'url' => $this->url,
                'checkin_summary' => $this->checkinSummary(),
            ]),
        ];
    }
}
