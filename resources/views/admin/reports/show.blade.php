@extends('layouts.admin')

@section('title', '通報')

@section('tab-content')
    <div class="container">
        <h2>通報 #{{ $report->id }}</h2>
        <hr>
        <dl>
            <dt>通報日時</dt>
            <dd>{{ $report->created_at->format('Y/m/d H:i:s') }}</dd>
            <dt>通報したユーザー</dt>
            <dd><a href="{{ route('user.profile', ['name' => $report->reporter->name]) }}">{{ $report->reporter->display_name }} (&commat;{{ $report->reporter->name }})</a></dd>
            <dt>通報対象ユーザー</dt>
            <dd>
                <a href="{{ route('user.profile', ['name' => $report->targetUser->name]) }}">{{ $report->targetUser->display_name }} (&commat;{{ $report->targetUser->name }})</a>
                <br>
                被通報回数: {{ $strikes }} 回
            </dd>
            <dt>理由</dt>
            <dd>{{ $report->violatedRule?->summary ?? 'その他' }}</dd>
            <dt>コメント</dt>
            <dd>
                @if (empty($report->comment))
                    &mdash;
                @else
                    {{ $report->comment }}
                @endif
            </dd>
        </dl>
        @if ($report->ejaculation !== null)
            <hr>
            <h4>対象のチェックイン</h4>
            <div class="card">
                <div class="card-body">
                    @component('components.ejaculation', ['ejaculation' => $report->ejaculation, 'showActions' => false])
                    @endcomponent
                </div>
            </div>
        @endif
    </div>
@endsection
