<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectController extends Controller
{
    public function create()
    {
        return view('collect');
    }
}
