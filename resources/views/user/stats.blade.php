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
    <div id="cal-heatmap" class="tis-contribution-graph"></div>
    <hr class="my-4">
    <h5 class="my-4">月別チェックイン回数</h5>
    <canvas id="monthly-graph" class="w-100"></canvas>
@endif
@endsection

@push('script')
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/moment@2.20.1/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/chart.js@2.7.1/dist/Chart.min.js"></script>
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

    (function () {
        var labels = [];
        var m = moment().date(1);
        while (labels.length < 12) {
            labels.push(m.format('YYYY/MM'));
            m = m.subtract(1, 'months');
        }
        labels.reverse();

        var context = document.getElementById('monthly-graph').getContext('2d');
        var chart = new Chart(context, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: @json($monthlyCounts),
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    display: false
                },
                elements: {
                    line: {
                        tension: 0
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    })();
</script>
@endpush