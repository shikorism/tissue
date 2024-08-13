@extends('setting.base')

@section('title', 'Webhook')

@section('tab-content')
    <h3>Incoming Webhook</h3>
    <hr>
    <p>さまざまなシステムと連携してチェックインを行うためのWebhook URLを作成することができます。APIドキュメントは<a href="{{ url('/apidoc.html') }}">こちら</a>から参照いただけます。</p>
    <h4>新規作成</h4>
    <div class="card mt-3">
        <div class="card-body">
            <h6 class="font-weight-bold">おことわり</h6>
            <p>Webhook APIは予告なく仕様変更を行う場合がございます。また、サーバに対する過剰なリクエストや、不審な公開チェックインを繰り返している場合には管理者の裁量によって予告なく無効化(削除)する場合があります。</p>
            <p>通常利用と同様、1分以内のチェックインは禁止されていることを考慮してください。また、テスト目的であれば非公開チェックインをご活用ください。</p>
            <hr>
            @if (count($webhooks) >= $webhooksLimit)
                <p class="my-0 text-danger">1ユーザーが作成可能なWebhookは、{{ $webhooksLimit }}件までに制限されています。</p>
            @else
                <form action="{{ route('setting.webhooks.store') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="name">名前 (メモ)</label>
                        <input id="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" type="text" required>
                        <small class="form-text text-muted">後で分かるように名前を付けておいてください。</small>
                        @if ($errors->has('name'))
                            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <button class="btn btn-primary" type="submit">新規作成</button>
                </form>
            @endif
        </div>
    </div>
    @if (!$webhooks->isEmpty())
        <h4 class="mt-4">作成済みのWebhook</h4>
        <div class="list-group mt-3">
        @foreach ($webhooks as $webhook)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="flex-grow-1 mr-2">
                    <div>{{ $webhook->name }}</div>
                    <input class="webhook-url form-control form-control-sm mt-1" type="text" value="{{ url('/api/webhooks/checkin/' . $webhook->id) }}" readonly>
                </div>
                <div class="ml-2">
                    <button class="btn btn-outline-secondary copy-to-clipboard" type="button" data-toggle="popover" data-trigger="manual" data-placement="top" data-content="コピーしました！">コピー</button>
                    <button class="btn btn-outline-danger" type="button" data-target="#deleteIncomingWebhookModal" data-id="{{ $webhook->id }}">削除</button>
                </div>
            </div>
        @endforeach
        </div>
    @endif

    @component('components.modal', ['id' => 'deleteIncomingWebhookModal'])
        @slot('title')
            削除確認
        @endslot
        Webhookを削除してもよろしいですか？
        @slot('footer')
            <form action="{{ route('setting.webhooks.destroy', ['webhook' => '@']) }}" method="post">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <button type="submit" class="btn btn-danger">削除</button>
            </form>
        @endslot
    @endcomponent
@endsection

@push('script')
    @vite('resources/assets/js/setting/webhooks.ts')
@endpush
