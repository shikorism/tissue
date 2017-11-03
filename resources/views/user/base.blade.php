@extends('layouts.base')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <img src="{{ $user->getProfileImageUrl(64) }}" class="rounded mb-1">
                        <h4 class="card-title"><a class="text-dark" href="{{ route('user.profile', ['name' => $user->name]) }}">{{ $user->display_name }}</a></h4>
                        <h6 class="card-subtitle mb-4"><a class="text-muted" href="{{ route('user.profile', ['name' => $user->name]) }}">&commat;{{ $user->name }}</a></h6>

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
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.profile' ? 'active' : '' }}" href="{{ route('user.profile', ['name' => $user->name]) }}">タイムライン</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.stats' ? 'active' : '' }}" href="{{ route('user.stats', ['name' => $user->name]) }}">グラフ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.okazu' ? 'active' : '' }}" href="{{ route('user.okazu', ['name' => $user->name]) }}">オカズ</a>
                    </li>
                </ul>
                <div class="tab-content">
                    @yield('tab-content')
                </div>
            </div>
        </div>
    </div>
@endsection
