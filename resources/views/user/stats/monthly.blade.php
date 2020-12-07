@extends('user.stats.base')

@section('title', $user->display_name . ' さんのグラフ')

@section('stats-content')
    <h5 class="my-4">時間別チェックイン回数</h5>
    <canvas id="hourly-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">曜日別チェックイン回数</h5>
    <canvas id="dow-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">最も使用したタグ</h5>
    @if ($tags->isEmpty())
        <div class="alert alert-secondary">
            データがありません
        </div>
    @else
        <table class="table table-striped border">
            <tbody>
            @foreach ($tags as $tag)
                <tr>
                    <td><a class="text-reset" href="{{ route('search', ['q' => $tag->name]) }}"><span class="oi oi-tag text-secondary mr-2"></span>{{ $tag->name }}</a></td>
                    <td class="text-right">{{ number_format($tag->count) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection

@push('script')
<script id="graph-data" type="application/json">@json($graphData)</script>
<script src="{{ mix('js/vendor/chart.js') }}"></script>
<script src="{{ mix('js/user/stats.js') }}"></script>
@endpush
