<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Validator;
use App\Ejaculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EjaculationController extends Controller
{
    public function create()
    {
        return view('ejaculation.checkin');
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'date' => 'required|date_format:Y/m/d',
            'time' => 'required|date_format:H:i',
            'note' => 'nullable|string|max:500',
        ])->after(function ($validator) use ($request) {
            // 日時の重複チェック
            if (!$validator->errors()->hasAny(['date', 'time'])) {
                $dt = $request->input('date') . ' ' . $request->input('time');
                if (Ejaculation::where(['user_id' => Auth::id(), 'ejaculated_date' => $dt])->count()) {
                    $validator->errors()->add('datetime', '既にこの日時にチェックインしているため、登録できません。');
                }
            }
        })->validate();

        $ejaculation = Ejaculation::create([
            'user_id' => Auth::id(),
            'ejaculated_date' => Carbon::createFromFormat('Y/m/d H:i', $request->input('date') . ' ' . $request->input('time')),
            'note' => $request->input('note') ?? '',
            'is_private' => $request->has('is_private') ?? false
        ]);

        return redirect()->route('checkin.show', ['id' => $ejaculation->id])->with('status', 'チェックインしました！');
    }

    public function show($id)
    {
        $ejaculation = Ejaculation::findOrFail($id);
        $user = User::findOrFail($ejaculation->user_id);

        // 1つ前のチェックインからの経過時間を求める
        $previousEjaculation = Ejaculation::select('ejaculated_date')
            ->where('user_id', $ejaculation->user_id)
            ->where('ejaculated_date', '<', $ejaculation->ejaculated_date)
            ->orderByDesc('ejaculated_date')
            ->first();
        if (!empty($previousEjaculation)) {
            $ejaculatedSpan = $ejaculation->ejaculated_date
                ->diff($previousEjaculation->ejaculated_date)
                ->format('%a日 %h時間 %i分');
        } else {
            $ejaculatedSpan = null;
        }

        return view('ejaculation.show')->with(compact('user', 'ejaculation', 'ejaculatedSpan'));
    }

    public function edit()
    {
        // TODO: not implemented
    }

    public function update()
    {
        // TODO: not implemented
    }

    public function destroy($id)
    {
        $ejaculation = Ejaculation::findOrFail($id);
        $user = User::findOrFail($ejaculation->user_id);
        $ejaculation->delete();
        return redirect()->route('user.profile', ['name' => $user->name])->with('status', '削除しました。');
    }
}