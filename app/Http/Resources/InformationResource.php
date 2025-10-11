<?php

namespace App\Http\Resources;

use App\Information;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => Information::CATEGORIES[$this->category]['slug'],
            'pinned' => $this->pinned,
            'title' => $this->title,
            'created_at' => $this->created_at,
        ];
    }
}
