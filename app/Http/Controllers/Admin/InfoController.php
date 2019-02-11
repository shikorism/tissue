<?php

namespace App\Http\Controllers\Admin;

use App\Information;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InfoController extends Controller
{
    public function index()
    {
        $informations = Information::query()
            ->select('id', 'category', 'pinned', 'title', 'created_at')
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.info.index')->with([
            'informations' => $informations,
            'categories' => Information::CATEGORIES
        ]);
    }

    public function create()
    {
        // TODO
    }

    public function edit($id)
    {
        $information = Information::findOrFail($id);

        return view('admin.info.edit')->with([
            'info' => $information,
            'categories' => Information::CATEGORIES
        ]);
    }

    public function update(Request $request)
    {
        // TODO
    }
}
