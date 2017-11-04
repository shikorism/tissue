@extends('layouts.base')

@push('head')
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-4">
            @component('components.profile', ['user' => Auth::user()])
            @endcomponent
        </div>
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">サイトからのお知らせ</div>
                <div class="list-group list-group-flush">
                    @foreach($informations as $info)
                        <a class="list-group-item" href="{{ route('info.show', ['id' => $info->id]) }}">
                            <span class="badge {{ $categories[$info->category]['class'] }}">{{ $categories[$info->category]['label'] }}</span> {{ $info->title }} <small class="text-secondary">- {{ $info->created_at->format('n月j日') }}</small>
                        </a>
                    @endforeach
                    <a href="{{ route('info') }}" class="list-group-item text-right">お知らせ一覧 &raquo;</a>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">ランキング</div>
                <div class="card-body">
                    <p class="card-text">参加しているランキングはありません。自信のあるお題を探して、参加登録してみませんか？</p>
                    <p class="card-text">参加登録をすると、定期的に集計されてここにあなたの順位が表示されます。</p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item text-right">ランキング一覧 &raquo;</a>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">穴兄弟レーダー</div>
                <div class="card-body">
                    <p class="card-text">
                        あなたがよく使うタグやオカズから、関連していそうなオカズリンクを探して表示しています。
                    </p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action">
                        <h5 class="mb-2 text-success">#タグ</h5>
                        <p class="mb-1">薄い本のタイトル</p>
                        <small>https://www.toranoana.jp/mailorder/article/**/****/**/**/**********.html</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <h5 class="mb-2 text-success">#タグ</h5>
                        <p class="mb-1">イラストのタイトル</p>
                        <small>https://www.pixiv.net/member_illust.php?mode=medium&illust_id=********</small>
                    </a>
                    <a href="#" class="list-group-item text-right">もっと見る &raquo;</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
