<h6 class="font-weight-bold"><span class="oi oi-timer"></span> 現在のセッション</h6>
@if (isset($currentSession))
    <p class="card-text mb-0">{{ $currentSession }}経過</p>
    <p class="card-text">({{ $latestEjaculation->ejaculated_date->format('Y/m/d H:i') }} にリセット)</p>
@else
    <p class="card-text mb-0">計測がまだ始まっていません</p>
    <p class="card-text">(一度チェックインすると始まります)</p>
@endif

<h6 class="font-weight-bold"><span class="oi oi-graph"></span> 概況</h6>
<p class="card-text mb-0">平均記録: {{ Formatter::formatInterval($average) }}</p>
<p class="card-text mb-0">最長記録: {{ Formatter::formatInterval($summary[0]->longest) }}</p>
<p class="card-text mb-0">最短記録: {{ Formatter::formatInterval($summary[0]->shortest) }}</p>
<p class="card-text mb-0">合計時間: {{ Formatter::formatInterval($summary[0]->total_times) }}</p>
<p class="card-text">通算回数: {{ number_format($total) }}回</p>
