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

        return view('user.collections')->with(compact('user'));
    }

    public function show($name, $id)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }

        return view('user.collections')->with(compact('user'));
    }
}
