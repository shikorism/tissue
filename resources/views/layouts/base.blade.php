<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tissue') }}</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/open-iconic-bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tissue.css') }}" rel="stylesheet">

    @stack('head')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light {{ !Auth::check() && Route::currentRouteName() === 'home' ? '' : 'mb-4'}}">
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>

    <div class="container">
        <a href="{{ route('home') }}" class="navbar-brand">{{ config('app.name', 'Tissue') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            @auth
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item {{ stripos(Route::currentRouteName(), 'home') === 0 ? 'active' : ''}}">
                        <a class="nav-link" href="{{ route('home') }}">ホーム</a>
                    </li>
                    <li class="nav-item {{ stripos(Route::currentRouteName(), 'user.profile') === 0 ? 'active' : ''}}">
                        <a class="nav-link" href="{{ route('user.profile', ['name' => Auth::user()->name]) }}">タイムライン</a>
                    </li>
                    <li class="nav-item {{ stripos(Route::currentRouteName(), 'user.stats') === 0 ? 'active' : ''}}">
                        <a class="nav-link" href="{{ route('user.stats', ['name' => Auth::user()->name]) }}">グラフ</a>
                    </li>
                    <li class="nav-item {{ stripos(Route::currentRouteName(), 'user.okazu') === 0 ? 'active' : ''}}">
                        <a class="nav-link" href="{{ route('user.okazu', ['name' => Auth::user()->name]) }}">オカズ</a>
                    </li>
                    {{--<li class="nav-item">
                        <a class="nav-link" href="{{ route('ranking') }}">ランキング</a>
                    </li>--}}
                </ul>
                <form action="{{ stripos(Route::currentRouteName(), 'search') === 0 ? route(Route::currentRouteName()) : route('search') }}" class="form-inline mr-2">
                    <div class="input-group">
                        <input type="search" name="q" class="form-control" placeholder="検索..." value="{{ stripos(Route::currentRouteName(), 'search') === 0 ? $inputs['q'] : '' }}" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit"><span class="oi oi-magnifying-glass" aria-hidden="true"></span><span class="sr-only">検索</span></button>
                        </div>
                    </div>
                </form>
                <form class="form-inline mr-2">
                    <a href="{{ route('checkin') }}" class="btn btn-outline-success">チェックイン</a>
                </form>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ Auth::user()->getProfileImageUrl(30) }}" width="30" height="30" class="rounded d-inline-block align-top">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                            <h6 class="dropdown-header">{{ Auth::user()->display_name }} さん</h6>
                            <div class="dropdown-divider"></div>
                            {{--<a href="#" class="dropdown-item">設定</a>--}}
                            <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
                        </div>
                    </li>
                </ul>
            @endauth
            @guest
                <ul class="navbar-nav ml-auto mr-2">
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="nav-link">会員登録</a>
                    </li>
                </ul>
                <form class="form-inline">
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">ログイン</a>
                </form>
            @endguest
        </div>
    </div>
</nav>
@if (session('status'))
<div class="container">
    <div id="status" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
@endif
@yield('content')
<footer class="tis-footer mt-4">
    <div class="container p-3 p-md-4">
        <p>Copyright (c) 2017 shikorism.net</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="https://github.com/shibafu528" class="text-dark">Admin(@shibafu528)</a></li>
            <li class="list-inline-item"><a href="https://github.com/shikorism/tissue" class="text-dark">GitHub</a></li>
        </ul>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
<script>
    $(function(){
        $('[data-toggle="tooltip"]').tooltip();
        $('.alert').alert();
        @if (session('status'))
        setTimeout(function () {
            $('#status').alert('close');
        }, 5000);
        @endif
    });
</script>
@stack('script')
</body>
</html>