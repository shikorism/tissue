<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::query()
            ->with([
                'violatedRule' => fn ($query) => $query->withTrashed(),
                'targetUser',
            ])
            ->orderByDesc('id')
            ->get();

        return view('admin.reports.index')->with(compact('reports'));
    }

    public function show(Report $report)
    {
        return view('admin.reports.show')->with(compact('report'));
    }
}
