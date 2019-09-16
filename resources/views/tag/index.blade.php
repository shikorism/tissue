@extends('layouts.base')

@section('title', 'タグ一覧')

@section('content')
    <div class="container pb-1">
        <h2 class="mb-3">タグ一覧</h2>
        <p class="text-secondary">公開チェックインに付けられているタグを、チェックイン数の多い順で表示しています。</p>
    </div>
    <div class="container-fluid">
        <div class="row mx-1">
            @foreach($tags as $tag)
                <div class="col-12 col-lg-6 col-xl-3 py-3 text-break tags">
                    <a href="{{ route('search', ['q' => $tag->name]) }}" class="btn btn-outline-primary btn-tag" title="{{ $tag->name }}"><span class="tag-name">{{ $tag->name }}</span> <span class="checkins-count">({{ $tag->checkins_count }})</span></a>
                </div>
            @endforeach
        </div>
        {{ $tags->links(null, ['className' => 'mt-4 justify-content-center']) }}
    </div>
@endsection
