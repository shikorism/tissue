<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Moderation;
use App\ModerationAction;
use App\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

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
            'moderations.moderator',
        ]);
        $strikes = Report::where('target_user_id', $report->target_user_id)->count();

        return view('admin.reports.show')->with(compact('report', 'strikes'));
    }

    public function action(Report $report, Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', new Enum(ModerationAction::class)],
            'comment' => 'string|max:100',
            'send_email' => 'boolean',
        ]);

        DB::transaction(function () use ($report, $validated) {
            $mod = new Moderation($validated);
            $mod->moderator()->associate(Auth::user());
            $mod->report()->associate($report);
            $mod->targetUser()->associate($report->targetUser);
            $mod->ejaculation()->associate($report->ejaculation);
            $mod->send_email |= false;
            $mod->saveOrFail();

            if (!$mod->performAction()) {
                abort(500);
            }
        });

        return redirect()->route('admin.reports.show', ['report' => $report])->with('status', 'モデレーションを実行しました。');
    }
}
