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

        return view('user.collections.show')->with(compact('user'));
    }
}
