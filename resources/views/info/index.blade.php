@extends('layouts.base')

@section('title', 'お知らせ')

@section('content')
<div class="container">
    <h2>サイトからのお知らせ</h2>
    <hr>
    <div class="list-group">
        @foreach($informations as $info)
        <a class="list-group-item border-bottom-only pt-3 pb-3" href="{{ route('info.show', ['id' => $info->id]) }}">
            @if ($info->pinned)
                <span class="badge bg-secondary"><span class="oi oi-pin"></span>ピン留め</span>
            @endif
            <span class="badge {{ $categories[$info->category]['class'] }}">{{ $categories[$info->category]['label'] }}</span> {{ $info->title }} <small class="text-secondary">- {{ $info->created_at->format('n月j日') }}</small>
        </a>
        @endforeach
    </div>
    {{ $informations->links(null, ['className' => 'mt-4 justify-content-center']) }}
</div>
@endsection
