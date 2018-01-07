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
            ->take(3)
            ->get();
        $categories = Information::CATEGORIES;

        if (Auth::check()) {
            // お惣菜コーナー用のデータ取得
            $publicLinkedEjaculations = Ejaculation::join('users', 'users.id', '=', 'ejaculations.user_id')
                ->where('users.is_protected', false)
                ->where('ejaculations.is_private', false)
                ->where('ejaculations.link', '<>', '')
                ->orderBy('ejaculations.ejaculated_date', 'desc')
                ->select('ejaculations.*')
                ->with('user')
                ->take(5)
                ->get();

            return view('home')->with(compact('informations', 'categories', 'publicLinkedEjaculations'));
        } else {
            return view('guest')->with(compact('informations', 'categories'));
        }
    }
}
