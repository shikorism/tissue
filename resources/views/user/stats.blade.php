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
    <h5 class="my-4">月間チェックイン回数</h5>
    <canvas id="monthly-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">年間チェックイン回数</h5>
    <canvas id="yearly-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">曜日別チェックイン回数</h5>
    <canvas id="dow-graph" class="w-100"></canvas>
@endif
@endsection

@push('script')
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
        weekStartOnMonday: false,
        start: new Date({{ \Carbon\Carbon::now()->addMonths(-9)->timestamp * 1000 }}),
        range: 10,
        data: @json($dailySum),
        legend: [1, 2, 3, 4]
    });

    function createLineGraph(id, labels, data) {
        var context = document.getElementById(id).getContext('2d');
        var chart = new Chart(context, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
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
    }

    function createBarGraph(id, labels, data) {
        var context = document.getElementById(id).getContext('2d');
        var chart = new Chart(context, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    display: false
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
    }

    createLineGraph('monthly-graph', @json(array_keys($monthlySum)), @json(array_values($monthlySum)));
    createLineGraph('yearly-graph', @json(array_keys($yearlySum)), @json(array_values($yearlySum)));
    createBarGraph('dow-graph', ['日', '月', '火', '水', '木', '金', '土'], @json($dowSum));
</script>
@endpush