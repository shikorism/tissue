@extends('layouts.base')

@section('head')
<style>
    #ejaculations .title {
        font-size: large;
    }
    #ejaculations .note {
        margin: 8px 0;
    }
    .no-border {
        border: none;
    }
    .pagination {
        padding-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col s12 m4">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">{{ $user->display_name }}</span>
                    <p>&commat;{{ $user->name }}</p>
                    <hr>
                    <p class="valign-wrapper"><i class="material-icons">av_timer</i><b>現在のセッション</b></p>
                    @if (isset($currentSession))
                        <p>{{ $currentSession }}経過</p>
                        <p>({{ $ejaculations[0]['ejaculated_date'] }} にリセット)</p>
                    @else
                        <p>計測がまだ始まっていません</p>
                        @if (Auth::check() && $user->id === Auth::id())
                        <p>(一度チェックインすると始まります)</p>
                        @endif
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
        </div>
        <div class="col s12 m8">
            <div class="card">
                <div class="card-tabs">
                    <ul class="tabs tabs-fixed-width">
                        <li class="tab col s6"><a href="#ejaculations">チェックイン</a></li>
                        {{--<li class="tab col s6"><a href="profile-graph.html" target="_self">グラフ</a></li>--}}
                    </ul>
                    <div class="card-panel no-padding">
                        <ul id="ejaculations" class="collection no-border">
                            @forelse ($ejaculations as $ejaculation)
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
                            @empty
                                <li class="collection-item">
                                    <div class="note">
                                        まだチェックインしていません。
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                        <ul class="pagination center">
                            <li class="{{ $ejaculations->currentPage() === 1 ? 'disabled' : 'waves-effect' }}"><a href="{{ $ejaculations->previousPageUrl() }}"><i class="material-icons">chevron_left</i></a></li>
                            @for ($i = 1; $i <= $ejaculations->lastPage(); $i++)
                                <li class="{{ $i === $ejaculations->currentPage() ? 'active' : 'waves-effect' }}"><a href="{{ $ejaculations->url($i) }}">{{ $i }}</a></li>
                            @endfor
                            <li class="{{ $ejaculations->currentPage() === $ejaculations->lastPage() ? 'disabled' : 'waves-effect' }}"><a href="{{ $ejaculations->nextPageUrl() }}"><i class="material-icons">chevron_right</i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
