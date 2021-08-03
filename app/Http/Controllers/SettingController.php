<?php

namespace App\Http\Controllers;

use App\CheckinWebhook;
use App\DeactivatedUser;
use App\Ejaculation;
use App\Exceptions\CsvImportException;
use App\Mail\PasswordChanged;
use App\Services\CheckinCsvExporter;
use App\Services\CheckinCsvImporter;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\TokenRepository;

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
            'display_name' => 'required|string|max:20',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore(Auth::user()->email, 'email')
            ],
            'bio' => 'nullable|string|max:160',
            'url' => 'nullable|url|max:2000'
        ], [], [
            'display_name' => '名前',
            'email' => 'メールアドレス',
            'bio' => '自己紹介',
            'url' => 'URL'
        ]);

        if ($validator->fails()) {
            return redirect()->route('setting')->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $user->display_name = $inputs['display_name'];
        $user->email = $inputs['email'];
        $user->bio = $inputs['bio'] ?? '';
        $user->url = $inputs['url'] ?? '';
        $user->save();

        return redirect()->route('setting')->with('status', 'プロフィールを更新しました。');
    }

    public function privacy()
    {
        return view('setting.privacy');
    }

    public function updatePrivacy(Request $request)
    {
        $inputs = $request->all(['is_protected', 'accept_analytics', 'private_likes']);

        $user = Auth::user();
        $user->is_protected = $inputs['is_protected'] ?? false;
        $user->accept_analytics = $inputs['accept_analytics'] ?? false;
        $user->private_likes = $inputs['private_likes'] ?? false;
        $user->save();

        return redirect()->route('setting.privacy')->with('status', 'プライバシー設定を更新しました。');
    }

    public function webhooks()
    {
        $webhooks = Auth::user()->checkinWebhooks;
        $webhooksLimit = CheckinWebhook::PER_USER_LIMIT;

        return view('setting.webhooks')->with(compact('webhooks', 'webhooksLimit'));
    }

    public function storeWebhooks(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('checkin_webhooks', 'name')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ]
        ], [], [
            'name' => '名前'
        ]);

        if (Auth::user()->checkinWebhooks()->count() >= CheckinWebhook::PER_USER_LIMIT) {
            return redirect()->route('setting.webhooks')
                ->with('status', CheckinWebhook::PER_USER_LIMIT . '件以上のWebhookを作成することはできません。');
        }

        Auth::user()->checkinWebhooks()->create($validated);

        return redirect()->route('setting.webhooks')->with('status', '作成しました。');
    }

    public function destroyWebhooks(CheckinWebhook $webhook)
    {
        $webhook->delete();

        return redirect()->route('setting.webhooks')->with('status', '削除しました。');
    }

    public function import()
    {
        return view('setting.import');
    }

    public function storeImport(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file'
        ], [], [
            'file' => 'ファイル'
        ]);

        $file = $request->file('file');
        if (!$file->isValid()) {
            return redirect()->route('setting.import')->withErrors(['file' => 'ファイルのアップロードに失敗しました。']);
        }

        try {
            set_time_limit(0);

            $importer = new CheckinCsvImporter(Auth::user(), $file->path());
            $imported = $importer->execute();

            return redirect()->route('setting.import')->with('status', "{$imported}件のインポートに性交しました。");
        } catch (CsvImportException $e) {
            return redirect()->route('setting.import')->with('import_errors', $e->getErrors());
        }
    }

    public function destroyImport()
    {
        Auth::user()
            ->ejaculations()
            ->where('ejaculations.source', Ejaculation::SOURCE_CSV)
            ->delete();

        return redirect()->route('setting.import')->with('status', '削除が完了しました。');
    }

    public function export()
    {
        return view('setting.export');
    }

    public function exportToCsv(Request $request)
    {
        $validated = $request->validate([
            'charset' => ['required', Rule::in(['utf8', 'sjis'])]
        ]);

        $charsets = [
            'utf8' => 'UTF-8',
            'sjis' => 'SJIS-win'
        ];

        $filename = tempnam(sys_get_temp_dir(), 'tissue_export_tmp_');
        try {
            // 気休め
            set_time_limit(0);

            $exporter = new CheckinCsvExporter(Auth::user(), $filename, $charsets[$validated['charset']]);
            $exporter->execute();
        } catch (\Throwable $e) {
            unlink($filename);
            throw $e;
        }

        return response()
            ->download($filename, 'TissueCheckin_' . date('Y-m-d_H-i-s') . '.csv')
            ->deleteFileAfterSend(true);
    }

    public function deactivate()
    {
        return view('setting.deactivate');
    }

    public function destroyUser(Request $request)
    {
        // パスワードチェック
        $validated = $request->validate([
            'password' => 'required|string'
        ]);

        if (!Hash::check($validated['password'], Auth::user()->getAuthPassword())) {
            throw ValidationException::withMessages([
                'password' => 'パスワードが正しくありません。'
            ]);
        }

        // データの削除
        set_time_limit(0);
        DB::transaction(function () {
            $user = Auth::user();

            // 関連レコードの削除
            // TODO: 別にDELETE文相当のクエリを一発発行するだけでもいい？
            foreach ($user->ejaculations as $ejaculation) {
                $ejaculation->delete();
            }
            foreach ($user->likes as $like) {
                $like->delete();
            }

            // 先にログアウトしないとユーザーは消せない
            Auth::logout();

            // ユーザーの削除
            $user->delete();

            // ユーザー名履歴に追記
            DeactivatedUser::create(['name' => $user->name]);
        });

        return view('setting.deactivated');
    }

    public function password()
    {
        return view('setting.password');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed|different:current_password',
        ]);

        if (!Hash::check($validated['current_password'], $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'current_password' => 'パスワードが正しくありません。'
            ]);
        }

        $user->password = Hash::make($validated['password']);
        $user->saveOrFail();

        Mail::to($user->email)->send(new PasswordChanged($user));

        return redirect()->route('setting.password')->with('status', 'パスワードを変更しました。');
    }
}
