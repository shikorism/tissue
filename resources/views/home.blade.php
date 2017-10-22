@extends('layouts.base-old')

@section('head')
<style>
    #ejaculations .title {
        font-size: large;
    }
    #ejaculations .note {
        margin: 8px 0;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col s12 m4">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">{{ Auth::user()->display_name }}</span>
                    <p>&commat;{{ Auth::user()->name }}</p>
                    <hr>
                    <p class="valign-wrapper"><i class="material-icons">av_timer</i><b>現在のセッション</b></p>
                    @if (isset($currentSession))
                        <p>{{ $currentSession }}経過</p>
                        <p>({{ $ejaculations[0]['ejaculated_date'] }} にリセット)</p>
                    @else
                        <p>計測がまだ始まっていません</p>
                        <p>(一度チェックインすると始まります)</p>
                    @endif
                    @if (isset($summary) && $summary[0]->total_checkins > 0)
                    <hr>
                    <p class="valign-wrapper"><i class="material-icons">assessment</i><b>概況</b></p>
                    <p>平均記録: {{ Formatter::formatInterval($summary[0]->average) }}</p>
                    <p>最長記録: {{ Formatter::formatInterval($summary[0]->longest) }}</p>
                    <p>最短記録: {{ Formatter::formatInterval($summary[0]->shortest) }}</p>
                    <p>合計時間: {{ Formatter::formatInterval($summary[0]->total_times) }}</p>
                    <p>通算回数: {{ $summary[0]->total_checkins }}回</p>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-content red lighten-1">
                    <span class="card-title">オープンβテスト中</span>
                    <p>予告なくサービスの中断や大幅な機能変更、時にはデータの損失が発生する可能性があります。</p>
                    <p>特に、データについてはなるべく保持できるよう努めますが、どうしようもないことも時には発生しますので予めご了承ください。</p>
                </div>
            </div>
        </div>
        <div class="col s12 m8">
            <ul id="ejaculations" class="collection z-depth-1">
                @forelse ($ejaculations as $ejaculation)
                    @if ($loop->first)
                        <li class="collection-item">
                            <span class="title"><b>最近のチェックイン</b></span>
                        </li>
                    @endif

                    <li class="collection-item">
                        <span class="title">{{ $ejaculation['ejaculated_span'] ?? '精通' }}</span> <span class="grey-text">{{ $ejaculation['before_date'] }}{{ !empty($ejaculation['before_date']) ? ' ～ ' : '' }}{{ $ejaculation['ejaculated_date'] }}</span>
                        <div class="note">
                            {{ $ejaculation['note'] }}
                        </div>
                        @if ($ejaculation['is_private'])
                            <span class="grey-text"><i class="material-icons tiny">lock</i> 非公開チェックイン</span>
                        @endif
                        {{--<div class="chip">結月ゆかり</div>
                        <div class="chip">琴葉茜</div>--}}
                    </li>

                    @if ($loop->index === 7)
                        <li class="collection-item">
                            <div class="center">
                                <a href="{{ route('profile') }}">もっと見る</a>
                            </div>
                        </li>
                        @break
                    @endif
                @empty
                    <li class="collection-item">
                        <div class="note">
                            まだチェックインがありません。右上のチェックインボタンから今すぐ精通！
                        </div>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

</div>
@endsection
