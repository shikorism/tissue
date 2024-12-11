@extends('user.stats.base')

@section('title', $user->display_name . ' さんのグラフ')

@section('stats-content')
    <h5 class="my-4">Shikontribution graph</h5>
    <div id="cal-heatmap" class="tis-contribution-graph"></div>
    <hr class="my-4">
    <div class="my-4 d-flex justify-content-between">
        <h5 class="my-0">月間チェックイン回数</h5>
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="compare">
            <label class="custom-control-label" for="compare">去年のデータも表示</label>
        </div>
    </div>
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
    <h5 class="mt-4 mb-2">最も使ったオカズ</h5>
    <p class="mb-4 text-secondary">2回以上使用したオカズのみ集計しています。</p>
    <ul class="list-group">
        @forelse ($mostFrequentlyUsedRanking as $index => $item)
            <li class="list-group-item border-bottom-only pt-3 pb-3 px-0 text-break">
                <p class="mb-1"><span class="mr-3 tis-rank-badge">{{ $index + 1 }}</span><span class="mr-2" style="font-size: 1.75rem">{{ $item->count }}</span>回</p>
                <div class="row mx-0">
                    @component('components.link-card', ['link' => $item->normalized_link, 'is_too_sensitive' => false])
                    @endcomponent
                    <p class="d-flex align-items-baseline mb-2 col-12 px-0">
                        <i class="ti ti-link mr-1"></i><a class="overflow-hidden" href="{{ $item->normalized_link }}" target="_blank" rel="noopener">{{ $item->normalized_link }}</a>
                    </p>
                </div>
                <div class="ejaculation-actions">
                    <button type="button" class="btn text-secondary"
                            data-toggle="tooltip" data-placement="bottom"
                            title="同じオカズでチェックイン" data-href="{{ route('checkin', ['link' => $item->normalized_link]) }}"><i class="ti ti-reload"></i></button>
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
@if ($compareData !== null)
<script id="compare-data" type="application/json">@json($compareData)</script>
@endif
@vite('resources/assets/js/user/stats.ts')
@endpush
