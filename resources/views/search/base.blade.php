@extends('layouts.base')

@section('title', $inputs['q'] . ' の検索結果')

@section('content')
    <div class="container">
        <h2 class="mb-4"><strong>{{ $inputs['q'] }}</strong> の検索結果</h2>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() === 'search' ? 'active' : '' }}" href="{{ route('search', $inputs) }}">チェックイン</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() === 'search.collection' ? 'active' : '' }}" href="{{ route('search.collection', $inputs) }}">コレクション</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() === 'search.related-tag' ? 'active' : '' }}" href="{{ route('search.related-tag', $inputs) }}">関連するタグ</a>
            </li>
        </ul>
        <div class="tab-content">
            <p class="my-3 text-secondary">{{ $results->total() }} 件見つかりました</p>
            @yield('tab-content')
        </div>
    </div>
@endsection
