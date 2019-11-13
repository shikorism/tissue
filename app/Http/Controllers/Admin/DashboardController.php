<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $pgVersion = DB::select('show server_version')[0]->server_version;

        return view('admin.dashboard')->with(compact('pgVersion'));
    }
}
