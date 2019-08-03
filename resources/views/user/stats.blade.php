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
    <div class="row my-4">
        <div class="col-12 col-lg-6 d-flex align-items-center">
            <h5 class="my-0">月間チェックイン回数</h5>
        </div>
        <div class="col-12 col-lg-6">
            <select id="monthly-term" class="form-control"></select>
        </div>
    </div>
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
<script id="graph-data" type="application/json">@json($graphData)</script>
<script src="{{ mix('js/vendor/chart.js') }}"></script>
<script src="{{ mix('js/user/stats.js') }}"></script>
@endpush
