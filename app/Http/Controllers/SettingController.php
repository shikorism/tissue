<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function profile()
    {
        return view('setting.profile');
    }

    public function updateProfile(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'display_name' => 'required|string|max:20'
        ], [], [
            'display_name' => '名前'
        ]);

        if ($validator->fails()) {
            return redirect()->route('setting')->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $user->display_name = $inputs['display_name'];
        $user->save();

        return redirect()->route('setting')->with('status', 'プロフィールを更新しました。');
    }

    public function privacy()
    {
        return view('setting.privacy');
    }

    public function updatePrivacy(Request $request)
    {
        $inputs = $request->all(['is_protected', 'accept_analytics']);

        $user = Auth::user();
        $user->is_protected = $inputs['is_protected'] ?? false;
        $user->accept_analytics = $inputs['accept_analytics'] ?? false;
        $user->save();

        return redirect()->route('setting.privacy')->with('status', 'プライバシー設定を更新しました。');
    }

    // ( ◠‿◠ )☛ここに気づいたか・・・消えてもらう ▂▅▇█▓▒░(’ω’)░▒▓█▇▅▂うわあああああああ
//    public function password()
//    {
//        abort(501);
//    }
}
