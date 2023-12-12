<h6 class="font-weight-bold"><i class="ti ti-clock-play"></i> 現在のセッション</h6>
@if (isset($currentSession))
    <p class="card-text mb-0">{{ $currentSession }}経過</p>
    <p class="card-text">({{ $latestEjaculation->ejaculated_date->format('Y/m/d H:i') }} にリセット)</p>
@else
    <p class="card-text mb-0">計測がまだ始まっていません</p>
    <p class="card-text">(一度チェックインすると始まります)</p>
@endif

<h6 class="font-weight-bold"><i class="ti ti-timeline"></i> 概況</h6>
<table class="tis-profile-stats-table">
    <tr>
        <th>平均記録</th>
        <td>{{ Formatter::formatInterval($average) }}</td>
    </tr>
    <tr>
        <th>中央値</th>
        <td>{{ Formatter::formatInterval($median) }}</td>
    </tr>
    <tr>
        <th>最長記録</th>
        <td>{{ Formatter::formatInterval($summary[0]->longest) }}</td>
    </tr>
    <tr>
        <th>最短記録</th>
        <td>{{ Formatter::formatInterval($summary[0]->shortest) }}</td>
    </tr>
    <tr>
        <th>合計時間</th>
        <td>{{ Formatter::formatInterval($summary[0]->total_times) }}</td>
    </tr>
    <tr>
        <th>通算回数</th>
        <td>{{ number_format($total) }}回</td>
    </tr>
</table>
