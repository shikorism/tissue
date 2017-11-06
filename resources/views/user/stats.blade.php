@extends('user.base')

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
    <div id="cal-heatmap"></div>
    <hr class="my-4">
@endif
@endsection

@push('script')
<script type="text/javascript" src="//d3js.org/d3.v3.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/cal-heatmap/3.3.10/cal-heatmap.min.js"></script>
<script>
    var cal = new CalHeatMap();
    cal.init({
        itemSelector: '#cal-heatmap',
        domain: 'month',
        subDomain: 'day',
        domainLabelFormat: '%Y/%m',
        start: new Date({{ \Carbon\Carbon::now()->addMonths(-9)->timestamp * 1000 }}),
        range: 10,
        data: @json($calendarData),
        legend: [1, 2, 3, 4]
    });
</script>
@endpush