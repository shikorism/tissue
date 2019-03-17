<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminInfoStoreRequest;
use App\Information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        return view('admin.info.create')->with([
            'categories' => Information::CATEGORIES
        ]);
    }

    public function store(AdminInfoStoreRequest $request)
    {
        $inputs = $request->all();
        if (!$request->has('pinned')) {
            $inputs['pinned'] = false;
        }

        $info = Information::create($inputs);

        return redirect()->route('admin.info.edit', ['info' => $info])->with('status', 'お知らせを更新しました。');
    }

    public function edit($id)
    {
        $information = Information::findOrFail($id);

        return view('admin.info.edit')->with([
            'info' => $information,
            'categories' => Information::CATEGORIES
        ]);
    }

    public function update(AdminInfoStoreRequest $request, Information $info)
    {
        $inputs = $request->all();
        if (!$request->has('pinned')) {
            $inputs['pinned'] = false;
        }

        $info->fill($inputs)->save();

        return redirect()->route('admin.info.edit', ['info' => $info])->with('status', 'お知らせを更新しました。');
    }

    public function destroy(Information $info)
    {
        $info->delete();

        return redirect()->route('admin.info')->with('status', 'お知らせを削除しました。');
    }
}
