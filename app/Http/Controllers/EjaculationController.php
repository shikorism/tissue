<?php

namespace App\Http\Controllers;

use App\User;
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
            $dt = $request->input('date') . ' ' . $request->input('time');
            if (Ejaculation::where(['user_id' => Auth::id(), 'ejaculated_date' => $dt])->count()) {
                $validator->errors()->add('datetime', '既にこの日時にチェックインしているため、登録できません。');
            }
        })->validate();

        Ejaculation::create([
            'user_id' => Auth::id(),
            'ejaculated_date' => $request->input('date') . ' ' . $request->input('time'),
            'note' => $request->input('note') ?? '',
            'is_private' => $request->has('is_private') ?? false
        ]);

        return redirect()->route('home')->with('status', 'チェックインしました！');
    }

    public function show()
    {
        // TODO: not implemented
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
        return redirect()->route('profile', ['name' => $user->name])->with('status', '削除しました。');
    }
}