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
        $report->load([
            'violatedRule' => fn ($query) => $query->withTrashed(),
        ]);
        $strikes = Report::where('target_user_id', $report->target_user_id)->count();

        return view('admin.reports.show')->with(compact('report', 'strikes'));
    }
}
