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

    public function updateProfile()
    {
        abort(501);
    }

    public function updatePrivacy(Request $request)
    {
        $inputs = $request->all(['is_protected', 'accept_analytics']);

        $user = Auth::user();
        $user->is_protected = $inputs['is_protected'] ?? false;
        $user->accept_analytics = $inputs['accept_analytics'] ?? false;
        $user->save();

        return redirect()->route('setting')->with('status', 'プライバシー設定を更新しました。');
    }

    // ( ◠‿◠ )☛ここに気づいたか・・・消えてもらう ▂▅▇█▓▒░(’ω’)░▒▓█▇▅▂うわあああああああ
//    public function password()
//    {
//        abort(501);
//    }
}
