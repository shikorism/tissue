@extends('user.stats.base')

@section('title', $user->display_name . ' さんのグラフ')

@push('head')
<link rel="stylesheet" href="//cdn.jsdelivr.net/cal-heatmap/3.3.10/cal-heatmap.css" />
@endpush

@section('stats-content')
    <h5 class="my-4">Shikontribution graph</h5>
    <div id="cal-heatmap" class="tis-contribution-graph"></div>
    <hr class="my-4">
    <h5 class="my-4">月間チェックイン回数</h5>
    <canvas id="monthly-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">時間別チェックイン回数</h5>
    <canvas id="hourly-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">曜日別チェックイン回数</h5>
    <canvas id="dow-graph" class="w-100"></canvas>
    <hr class="my-4">
    @include('user.stats.components.used-tags')
    <hr class="my-4">
    <h5 class="my-4">最も使ったオカズ</h5>
    <p class="text-secondary">2回以上使用したオカズのみ集計しています。</p>
    <ul class="list-group">
        @forelse ($mostFrequentlyUsedRanking as $index => $item)
            <li class="list-group-item border-bottom-only pt-3 pb-3 px-0 text-break">
                <p class="mb-1"><span class="mr-3 tis-rank-badge">{{ $index + 1 }}</span><span class="mr-2" style="font-size: 1.75rem">{{ $item->count }}</span>回</p>
                <div class="row mx-0">
                    @component('components.link-card', ['link' => $item->normalized_link, 'is_too_sensitive' => false])
                    @endcomponent
                    <p class="d-flex align-items-baseline mb-2 col-12 px-0">
                        <span class="oi oi-link-intact mr-1"></span><a class="overflow-hidden" href="{{ $item->normalized_link }}" target="_blank" rel="noopener">{{ $item->normalized_link }}</a>
                    </p>
                </div>
            </li>
        @empty
            <li class="list-group-item border-bottom-only">
                <p>この期間のチェックインが無いか、2回以上使用したオカズがありません。</p>
            </li>
        @endforelse
    </ul>
@endsection

@push('script')
<script id="graph-data" type="application/json">@json($graphData)</script>
<script src="{{ mix('js/vendor/chart.js') }}"></script>
<script src="{{ mix('js/user/stats.js') }}"></script>
@endpush
