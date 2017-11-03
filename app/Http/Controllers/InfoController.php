<?php

namespace App\Http\Controllers;

use App\Information;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index()
    {
        $informations = Information::query()
            ->select('id', 'category', 'pinned', 'title', 'created_at')
            ->orderBy('pinned')
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('info.index')->with([
            'informations' => $informations,
            'categories' => Information::CATEGORIES
        ]);
    }

    public function show($id)
    {
        $information = Information::findOrFail($id);
        return view('info.show')->with([
            'info' => $information,
            'category' => Information::CATEGORIES[$information->category]
        ]);
    }
}
