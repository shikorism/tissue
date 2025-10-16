<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InformationResource;
use App\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function latest()
    {
        $information = Information::query()
            ->select('id', 'category', 'pinned', 'title', 'created_at')
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        return InformationResource::collection($information);
    }
}
