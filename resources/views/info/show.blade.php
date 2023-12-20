@extends('layouts.base')

@section('title', $category['label'] . ': ' . $info->title)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('info') }}">サイトからのお知らせ</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $info->created_at->format('Y年n月j日') }}</li>
        </ol>
    </nav>
    <h2><span class="badge {{ $category['class'] }}">{{ $category['label'] }}</span> {{ $info->title }}</h2>
    <p class="text-secondary">
        @if ($info->pinned)
            <span class="badge badge-secondary"><i class="ti ti-pinned-filled"></i>ピン留め</span>
        @endif
        <i class="ti ti-calendar-event"></i> {{ $info->created_at->format('Y年n月j日') }}
    </p>
    @parsedown($info->content)
</div>
@endsection
