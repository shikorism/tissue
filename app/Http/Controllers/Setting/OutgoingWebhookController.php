<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\OutgoingWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OutgoingWebhookController extends Controller
{
    public function index()
    {
        $webhooks = Auth::user()->outgoingWebhooks;
        $webhooksLimit = OutgoingWebhook::PER_USER_LIMIT;

        return view('setting.outgoing-webhook.index')->with(compact('webhooks', 'webhooksLimit'));
    }

    public function create()
    {
        return view('setting.outgoing-webhook.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('outgoing_webhooks', 'name')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
            'url' => 'required|url|max:2000',
            'is_active' => 'nullable|boolean',
            'on_checkin_created' => 'nullable|boolean',
            'on_checkin_updated' => 'nullable|boolean',
            'on_checkin_deleted' => 'nullable|boolean',
        ], [], [
            'name' => '名前',
            'url' => 'URL',
        ]);

        if (Auth::user()->outgoingWebhooks()->count() >= OutgoingWebhook::PER_USER_LIMIT) {
            return redirect()->route('setting.outgoing-webhooks.create')
                ->with('status', OutgoingWebhook::PER_USER_LIMIT . '件以上のWebhookを登録することはできません。');
        }

        Auth::user()->outgoingWebhooks()->create($validated);

        return redirect()->route('setting.outgoing-webhooks')->with('status', '登録しました。');
    }

    public function edit(OutgoingWebhook $webhook)
    {
        $this->authorize('view', $webhook);

        return view('setting.outgoing-webhook.edit')->with(compact('webhook'));
    }

    public function update(OutgoingWebhook $webhook, Request $request)
    {
        $this->authorize('update', $webhook);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('outgoing_webhooks', 'name')->where(function ($query) use ($webhook) {
                    return $query->where('user_id', Auth::id())->where('id', '<>', $webhook->id);
                })
            ],
            'url' => 'required|url|max:2000',
            'is_active' => 'nullable|boolean',
            'on_checkin_created' => 'nullable|boolean',
            'on_checkin_updated' => 'nullable|boolean',
            'on_checkin_deleted' => 'nullable|boolean',
        ], [], [
            'name' => '名前',
            'url' => 'URL',
        ]);

        $webhook->update($validated);

        return redirect()->route('setting.outgoing-webhooks')->with('status', '更新しました。');
    }

    public function destroy(OutgoingWebhook $webhook)
    {
        $this->authorize('delete', $webhook);
        $webhook->delete();

        return redirect()->route('setting.outgoing-webhooks')->with('status', '削除しました。');
    }
}
