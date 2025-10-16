<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function __construct($resource, private $withCheckinSummary = false)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id, // private
            'name' => $this->name,
            'display_name' => $this->display_name,
            'is_protected' => $this->is_protected,
            'private_likes' => $this->is_protected || $this->private_likes, // 鍵垢の場合はフラグを秘匿
            'profile_image_url' => $this->getProfileImageUrl(256), // private
            'profile_mini_image_url' => $this->getProfileImageUrl(64), // private
            $this->mergeWhen($this->isMe() || !$this->is_protected, [
                'bio' => $this->bio,
                'url' => $this->url,
                'checkin_summary' => $this->when($this->withCheckinSummary, fn () => $this->checkinSummary()), // UserController等から直接指名で取得した時のみ含める
            ]),
        ];
    }
}
