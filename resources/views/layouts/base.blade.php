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

    @yield('head')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>

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
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile') }}">タイムライン</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile') }}">グラフ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile') }}">オカズ</a>
                </li>
                {{--<li class="nav-item">
                    <a class="nav-link" href="{{ route('ranking') }}">ランキング</a>
                </li>--}}
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ Auth::user()->getProfileImageUrl(30) }}" width="30" height="30" class="rounded d-inline-block align-top mr-2">
                        {{ Auth::user()->display_name }} さん
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        {{--<a href="#" class="dropdown-item">設定</a>--}}
                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
                    </div>
                </li>
            </ul>
            <form class="form-inline">
                <a href="{{ route('checkin') }}" class="btn btn-outline-success">チェックイン</a>
            </form>
        @endauth
        @guest
            <form class="form-inline">
                <a href="{{ route('login') }}" class="btn btn-outline-success">ログイン</a>
            </form>
        @endguest
    </div>
</nav>
@yield('content')
<footer class="tis-footer mt-4">
    <div class="container-fluid p-3 p-md-4">
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
    });
</script>
@yield('script')
</body>
</html>