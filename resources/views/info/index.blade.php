@extends('layouts.base')

@section('title', 'お知らせ')

@section('content')
<div class="container">
    <h2>サイトからのお知らせ</h2>
    <hr>
    <div class="list-group">
        @foreach($informations as $info)
        <a class="list-group-item border-bottom-only pt-3 pb-3" href="{{ route('info.show', ['id' => $info->id]) }}">
            <span class="badge {{ $categories[$info->category]['class'] }}">{{ $categories[$info->category]['label'] }}</span> {{ $info->title }} <small class="text-secondary">- {{ $info->created_at->format('n月j日') }}</small>
        </a>
        @endforeach
    </div>
    <ul class="pagination mt-4 justify-content-center">
        <li class="page-item {{ $informations->currentPage() === 1 ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $informations->previousPageUrl() }}" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
        @for ($i = 1; $i <= $informations->lastPage(); $i++)
            <li class="page-item {{ $i === $informations->currentPage() ? 'active' : '' }}"><a href="{{ $informations->url($i) }}" class="page-link">{{ $i }}</a></li>
        @endfor
        <li class="page-item {{ $informations->currentPage() === $informations->lastPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $informations->nextPageUrl() }}" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>
</div>
@endsection