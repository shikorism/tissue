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
