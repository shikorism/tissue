@extends('layouts.admin')

@section('title', '通報')

@section('tab-content')
    <div class="container">
        <h2>通報</h2>
        <hr>
        <table class="table table-sm">
            <thead>
            <tr>
                <th>ID</th>
                <th>通報日時</th>
                <th>対象</th>
                <th>理由</th>
            </tr>
            </thead>
            <tbody>
            @foreach($reports as $report)
                <tr>
                    <td><a href="{{ route('admin.reports.show', ['report' => $report]) }}">{{ $report->id }}</a></td>
                    <td><a href="{{ route('admin.reports.show', ['report' => $report]) }}">{{ $report->created_at->format('Y/m/d H:i:s') }}</a></td>
                    <td>
                        @if ($report->ejaculation_id !== null)
                            <a href="{{ route('checkin.show', ['id' => $report->ejaculation_id]) }}" target="_blank">checkin/{{ $report->ejaculation_id }}</a>
                        @else
                            <a href="{{ route('user.profile', ['name' => $report->targetUser->name]) }}" target="_blank">user/{{ $report->targetUser->name }}</a>
                        @endif
                    </td>
                    <td>{{ $report->violatedRule?->summary ?? 'その他' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
