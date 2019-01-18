<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function updatePrivacy()
    {
        abort(501);
    }

    // ( ◠‿◠ )☛ここに気づいたか・・・消えてもらう ▂▅▇█▓▒░(’ω’)░▒▓█▇▅▂うわあああああああ
//    public function password()
//    {
//        abort(501);
//    }
}
