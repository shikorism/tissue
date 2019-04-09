@extends('layouts.base')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                @component('components.profile', ['user' => $user])
                @endcomponent
                @section('sidebar')
                @show
            </div>
            <div class="col-lg-8">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.profile' ? 'active' : '' }}" href="{{ route('user.profile', ['name' => $user->name]) }}">タイムライン</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.stats' ? 'active' : '' }}" href="{{ route('user.stats', ['name' => $user->name]) }}">グラフ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.okazu' ? 'active' : '' }}" href="{{ route('user.okazu', ['name' => $user->name]) }}">オカズ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.likes' ? 'active' : '' }}" href="{{ route('user.likes', ['name' => $user->name]) }}">いいね
                            @if ($user->isMe() || !($user->is_protected || $user->private_likes))
                                <span class="badge badge-primary">{{ $user->likes()->count() }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    @yield('tab-content')
                </div>
            </div>
        </div>
    </div>
@endsection
