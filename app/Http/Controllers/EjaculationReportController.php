<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use App\Http\Requests\EjaculationReportRequest;
use App\Mail\EjaculationReported;
use App\Report;
use App\Rule;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EjaculationReportController extends Controller
{
    public function create(Ejaculation $ejaculation)
    {
        $rules = Rule::query()->sorted()->get();

        return view('ejaculation.report')->with(compact('ejaculation', 'rules'));
    }

    public function store(EjaculationReportRequest $request, Ejaculation $ejaculation)
    {
        $validated = $request->validated();

        $report = new Report();
        $report->comment = $validated['comment'];
        $report->reporter()->associate(Auth::user());
        $report->targetUser()->associate($ejaculation->user);
        $report->ejaculation()->associate($ejaculation);

        if (!empty($validated['violated_rule']) && $validated['violated_rule'] !== 'other') {
            $rule = Rule::findOrFail($validated['violated_rule']);
            $report->violatedRule()->associate($rule);
        }

        $report->save();

        Mail::to(User::administrators()->get())->send(new EjaculationReported($report));

        return redirect()->route('checkin.show', ['id' => $ejaculation->id])->with('status', 'チェックインを報告しました。');
    }
}
