<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use App\Information;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $informations = Information::query()
            ->select('id', 'category', 'pinned', 'title', 'created_at')
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->paginate(3);
        $categories = Information::CATEGORIES;

        if (Auth::check()) {
            return view('home')->with(compact('informations', 'categories'));
        } else {
            return view('guest')->with(compact('informations', 'categories'));
        }
    }
}
