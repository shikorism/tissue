<?php

namespace App\Http\Controllers\Api;

use App\Ejaculation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckinController extends Controller
{
    public function destroy(Request $request, $checkin)
    {
        $ejaculation = Ejaculation::with('user')->find($checkin);

        if ($ejaculation !== null) {
            $this->authorize('edit', $ejaculation);

            DB::transaction(function () use ($ejaculation) {
                $ejaculation->tags()->detach();
                $ejaculation->delete();
            });
        }

        if ($request->input('flash') === true) {
            session()->flash('status', '削除しました。');
        }

        return response()->json([
            'user' => $ejaculation !== null ? route('user.profile', ['name' => $ejaculation->user->name]) : null
        ]);
    }
}
