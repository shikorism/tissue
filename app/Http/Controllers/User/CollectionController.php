<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index($name)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }

        $collection = $user->collections()->first();
        if ($collection) {
            return redirect()->route('user.collections.show', ['name' => $name, 'id' => $collection->id]);
        }

        return view('user.collections.index')->with(compact('user'));
    }

    public function show($name, $id)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }
        if ($user->is_protected && !$user->isMe()) {
            return redirect()->route('user.collections', ['name' => $name]);
        }

        $collections = $user->collections;
        $collection = $user->collections()->findOrFail($id);
        $items = $collection->items()->paginate(20);

        return view('user.collections.show')->with(compact('user', 'collections', 'collection', 'items'));
    }
}
