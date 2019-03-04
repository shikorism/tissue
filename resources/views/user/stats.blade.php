@extends('user.base')

@section('title', $user->display_name . ' さんのグラフ')

@push('head')
<link rel="stylesheet" href="//cdn.jsdelivr.net/cal-heatmap/3.3.10/cal-heatmap.css" />
@endpush

@section('tab-content')
@if ($user->is_protected && !$user->isMe())
    <p class="mt-4">
        <span class="oi oi-lock-locked"></span> このユーザはチェックイン履歴を公開していません。
    </p>
@else
    <h5 class="my-4">Shikontribution graph</h5>
    <div id="cal-heatmap" class="tis-contribution-graph"></div>
    <hr class="my-4">
    <h5 class="my-4">月間チェックイン回数</h5>
    <canvas id="monthly-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">年間チェックイン回数</h5>
    <canvas id="yearly-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">時間別チェックイン回数</h5>
    <canvas id="hourly-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">曜日別チェックイン回数</h5>
    <canvas id="dow-graph" class="w-100"></canvas>
@endif
@endsection

@push('script')
<script id="graph-daily-sum" type="application/javascript">@json($dailySum)</script>
<script id="graph-monthly-key" type="application/javascript">@json(array_keys($monthlySum))</script>
<script id="graph-monthly-sum" type="application/javascript">@json(array_values($monthlySum))</script>
<script id="graph-yearly-key" type="application/javascript">@json(array_keys($yearlySum))</script>
<script id="graph-yearly-sum" type="application/javascript">@json(array_values($yearlySum))</script>
<script id="graph-hourly-key" type="application/javascript">@json(array_keys($hourlySum))</script>
<script id="graph-hourly-sum" type="application/javascript">@json(array_values($hourlySum))</script>
<script id="graph-dow-sum" type="application/javascript">@json($dowSum)</script>
<script src="{{ mix('js/user/stats.js') }}"></script>
@endpush