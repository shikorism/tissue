@extends('setting.base')

@section('title', 'Outgoing Webhook')

@section('tab-content')
    <h3>Outgoing Webhook</h3>
    <hr>
    <p>Webhookを利用することで、チェックインをさまざまなシステムに連携することができます。APIドキュメントは<a href="{{ url('/apidoc.html') }}">こちら</a>から参照いただけます。</p>
    <h4>新規登録</h4>
    <div class="card mt-3">
        <div class="card-body">
            <h6 class="font-weight-bold">おことわり</h6>
            <p>Webhook APIは予告なく仕様変更を行う場合がございます。また、登録したWebhookに起因してサーバに過剰な負荷が発生した場合、管理者の裁量によって予告なく無効化する場合があります。</p>
            <hr>
            <p class="mb-0">
                @if (count($webhooks) >= $webhooksLimit)
                    <a class="btn btn-primary disabled">新規登録</a>
                    <span class="ml-2 text-danger">1ユーザーが登録可能なWebhookは、{{ $webhooksLimit }}件までに制限されています。</span>
                @else
                    <a class="btn btn-primary" href="{{ route('setting.outgoing-webhooks.create') }}">新規登録</a>
                @endif
            </p>
        </div>
    </div>
    @if (!$webhooks->isEmpty())
        <h4 class="mt-4">登録済みのWebhook</h4>
        <div class="list-group mt-3">
        @foreach ($webhooks as $webhook)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="flex-grow-1 mr-2">
                    <div><span class="mr-1 ti {{ $webhook->is_active ? 'ti-player-play-filled text-success' : 'ti-player-pause-filled text-secondary' }}"></span>{{ $webhook->name }}</div>
                    <div class="text-secondary small text-break">{{ $webhook->url }}</div>
                </div>
                <div class="ml-2">
                    <a class="btn btn-outline-secondary" href="{{ route('setting.outgoing-webhooks.edit', ['webhook' => $webhook->id]) }}">編集</a>
                </div>
            </div>
        @endforeach
        </div>
    @endif
@endsection
