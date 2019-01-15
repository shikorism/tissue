@extends('layouts.base')

@section('title', $inputs['q'] . ' の検索結果')

@section('content')
    <div class="container">
        <h2 class="mb-4"><strong>{{ $inputs['q'] }}</strong> の検索結果</h2>
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'search' ? 'active' : '' }}" href="{{ route('search', $inputs) }}">チェックイン</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'search.related-tag' ? 'active' : '' }}" href="{{ route('search.related-tag', $inputs) }}">関連するタグ</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="card-body pb-0">
                    <p class="my-3 text-secondary">{{ $results->total() }} 件見つかりました</p>
                </div>
                @yield('tab-content')
            </div>
        </div>
    </div>
@endsection