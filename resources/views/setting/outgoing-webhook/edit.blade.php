@extends('setting.base')

@section('title', 'Outgoing Webhook')

@section('tab-content')
    <h3><span class="text-secondary">Outgoing Webhook / </span>編集</h3>
    <hr>
    <form action="{{ route('setting.outgoing-webhooks.update', ['webhook' => $webhook->id]) }}" method="post">
        {{ method_field('PUT') }}
        {{ csrf_field() }}

        <div class="form-group">
            <label for="name">名前 (メモ)</label>
            <input id="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" type="text" value="{{ old('name', $webhook->name) }}" autocomplete="off" required>
            <small class="form-text text-muted">後で分かるように名前を付けておきましょう。</small>
            @if ($errors->has('name'))
                <div class="invalid-feedback">{{ $errors->first('name') }}</div>
            @endif
        </div>

        <div class="form-group">
            <label for="url">送信先URL</label>
            <input id="url" class="form-control {{ $errors->has('url') ? ' is-invalid' : '' }}" name="url" type="text" value="{{ old('url', $webhook->url) }}" required>
            @if ($errors->has('url'))
                <div class="invalid-feedback">{{ $errors->first('url') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <p class="mb-1">購読するイベント</p>
            <p class="mb-2 small text-muted">ここで指定したイベントの発生時に通知が送信されます。</p>

            <div class="custom-control custom-checkbox mb-1">
                <input type="hidden" name="on_checkin_created" value="0">
                <input id="on_checkin_created" name="on_checkin_created" type="checkbox" class="custom-control-input" value="1" {{ old('on_checkin_created', $webhook->on_checkin_created) ? 'checked' : '' }}>
                <label class="custom-control-label" for="on_checkin_created">チェックインの登録</label>
            </div>
            <div class="custom-control custom-checkbox mb-1">
                <input type="hidden" name="on_checkin_updated" value="0">
                <input id="on_checkin_updated" name="on_checkin_updated" type="checkbox" class="custom-control-input" value="1" {{ old('on_checkin_updated', $webhook->on_checkin_updated) ? 'checked' : '' }}>
                <label class="custom-control-label" for="on_checkin_updated">チェックインの編集</label>
            </div>
            <div class="custom-control custom-checkbox mb-1">
                <input type="hidden" name="on_checkin_deleted" value="0">
                <input id="on_checkin_deleted" name="on_checkin_deleted" type="checkbox" class="custom-control-input" value="1" {{ old('on_checkin_deleted', $webhook->on_checkin_deleted) ? 'checked' : '' }}>
                <label class="custom-control-label" for="on_checkin_deleted">チェックインの削除</label>
            </div>
        </div>

        <hr>

        <div class="custom-control custom-checkbox mb-3">
            <input type="hidden" name="is_active" value="0">
            <input id="is_active" name="is_active" type="checkbox" class="custom-control-input" value="1" {{ old('is_active', $webhook->is_active) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_active">有効</label>
        </div>

        <div class="d-flex justify-content-between">
            <button class="btn btn-primary" type="submit">更新</button>
            <button class="btn btn-outline-danger" type="button" data-toggle="modal" data-target="#deleteModal">削除</button>
        </div>
    </form>

    <h3 class="mt-5">送信履歴</h3>
    <hr>
    <p>直近10件のWebhook送信履歴を確認することができます。</p>
    @forelse($deliveries as $delivery)
        <div class="card mb-3">
            <div class="card-body">
                <p class="mb-1"><b>送信日時</b>: {{ $delivery->created_at->format('Y/m/d H:i:s') }}</p>
                <p class="mb-1"><b>結果</b>:
                    @if ($delivery->is_success)
                        <span class="text-success font-weight-bold">成功</span>
                    @else
                        <span class="text-danger font-weight-bold">失敗</span>
                    @endif
                </p>
                <p class="mb-1"><b>イベント</b>: <code class="px-2 py-1 bg-light border rounded">{{ $delivery->event }}</code></p>
                <p class="mb-1"><b>Delivery ID</b>: <code class="px-2 py-1 bg-light border rounded">{{ $delivery->delivery_id }}</code></p>
                <p class="mb-1"><b>リクエストボディ</b>:</p>
                <pre class="p-2 bg-light border rounded">{{ json_encode(json_decode($delivery->request_body, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                <p class="mb-1"><b>ステータスコード</b>: <code class="px-2 py-1 bg-light border rounded">{{ $delivery->status_code ?? '-' }}</code></p>
                <p class="mb-1"><b>レスポンスボディ</b>:</p>
                <pre class="p-2 bg-light border rounded">{{ $delivery->response_body }}</pre>
            </div>
        </div>
    @empty
        <p class="font-weight-bold">送信履歴がありません。</p>
    @endforelse

    @component('components.modal', ['id' => 'deleteModal'])
        @slot('title')
            削除確認
        @endslot
        Webhookを削除してもよろしいですか？
        @slot('footer')
            <form action="{{ route('setting.outgoing-webhooks.destroy', ['webhook' => $webhook->id]) }}" method="post">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <button type="submit" class="btn btn-danger">削除</button>
            </form>
        @endslot
    @endcomponent
@endsection
