@extends('user.base')

@section('sidebar')
    @if (!$user->is_protected || $user->isMe())
        <div class="nav d-none d-lg-flex flex-column nav-pills" aria-orientation="vertical">
            <a class="nav-link {{ Route::currentRouteName() === 'user.stats' ? 'active' : '' }}"
               href="{{ route('user.stats', ['name' => $user->name]) }}">全期間</a>
            @foreach ($availableMonths as $year => $months)
                <div class="border-top mt-1">
                    <a class="nav-link mt-1 {{ Route::currentRouteName() === 'user.stats.yearly' && $currentYear === $year ? 'active' : '' }}"
                       href="{{ route('user.stats.yearly', ['name' => $user->name, 'year' => $year]) }}">{{ $year }}年</a>
                    @foreach ($months as $month)
                        <a class="nav-link ml-3 {{ Route::currentRouteName() === 'user.stats.monthly' && $currentYear === $year && $currentMonth === $month ? 'active' : '' }}"
                           href="{{ route('user.stats.monthly', ['name' => $user->name, 'year' => $year, 'month' => $month]) }}">{{ $month }}月</a>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('tab-content')
    @if ($user->is_protected && !$user->isMe())
        <p class="mt-4">
            <i class="ti ti-lock"></i> このユーザはチェックイン履歴を公開していません。
        </p>
    @else
        <div class="row my-2 d-lg-none">
            <div class="col-12 text-secondary font-weight-bold small">グラフの対象期間</div>
            <div class="col-6 text-secondary small">年</div>
            <div class="col-6 text-secondary small">月</div>
            <div class="col-12">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            {{ Route::currentRouteName() === 'user.stats' ? '全期間' : "{$currentYear}年" }}
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('user.stats', ['name' => $user->name]) }}" class="dropdown-item">全期間</a>
                            @foreach ($availableMonths as $year => $months)
                                <a href="{{ route('user.stats.yearly', ['name' => $user->name, 'year' => $year]) }}" class="dropdown-item">{{ $year }}年</a>
                            @endforeach
                        </div>
                    </li>
                    @if (Route::currentRouteName() === 'user.stats')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle disabled"
                               href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">全期間</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle"
                               href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                {{ Route::currentRouteName() === 'user.stats.yearly' ? '全期間' : "{$currentMonth}月" }}
                            </a>
                            <div class="dropdown-menu">
                                <a href="{{ route('user.stats.yearly', ['name' => $user->name, 'year' => $currentYear]) }}" class="dropdown-item">全期間</a>
                                @foreach ($availableMonths[$currentYear] as $month)
                                    <a href="{{ route('user.stats.monthly', ['name' => $user->name, 'year' => $currentYear, 'month' => $month]) }}" class="dropdown-item">{{ $month }}月</a>
                                @endforeach
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="row d-lg-none no-gutters border-bottom"></div>
        @yield('stats-content')
    @endif
@endsection
