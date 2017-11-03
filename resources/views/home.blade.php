@extends('layouts.base')

@push('head')
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <img src="{{ Auth::user()->getProfileImageUrl(64) }}" class="rounded mb-1">
                    <h4 class="card-title"><a class="text-dark" href="{{ route('user.profile') }}">{{ Auth::user()->display_name }}</a></h4>
                    <h6 class="card-subtitle mb-4"><a class="text-muted" href="{{ route('user.profile') }}">&commat;{{ Auth::user()->name }}</a></h6>

                    <h6 class="font-weight-bold"><span class="oi oi-timer"></span> 現在のセッション</h6>
                    @if (isset($currentSession))
                        <p class="card-text mb-0">{{ $currentSession }}経過</p>
                        <p class="card-text">({{ $ejaculations[0]['ejaculated_date'] }} にリセット)</p>
                    @else
                        <p class="card-text mb-0">計測がまだ始まっていません</p>
                        <p class="card-text">(一度チェックインすると始まります)</p>
                    @endif

                    <h6 class="font-weight-bold"><span class="oi oi-graph"></span> 概況</h6>
                    <p class="card-text mb-0">平均記録: {{ Formatter::formatInterval($summary[0]->average) }}</p>
                    <p class="card-text mb-0">最長記録: {{ Formatter::formatInterval($summary[0]->longest) }}</p>
                    <p class="card-text mb-0">最短記録: {{ Formatter::formatInterval($summary[0]->shortest) }}</p>
                    <p class="card-text mb-0">合計時間: {{ Formatter::formatInterval($summary[0]->total_times) }}</p>
                    <p class="card-text">通算回数: {{ $summary[0]->total_checkins }}回</p>
                </div>
            </div>
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
