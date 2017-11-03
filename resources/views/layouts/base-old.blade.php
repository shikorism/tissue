<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tissue') }}</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{ asset('css/materialize.min.css') }}" rel="stylesheet" media="screen,projection">

    @yield('head')
</head>
<body>
<nav class="grey lighten-1" role="navigation">
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    @if (Auth::check())
        <ul id="accountMenu" class="dropdown-content">
            <li><a href="{{ route('user.profile') }}">プロフィール</a></li>
            <li class="divider"></li>
            <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a></li>
        </ul>
    @endif
    <div class="nav-wrapper container">
        <a id="logo-container" href="{{ route('home') }}" class="brand-logo">{{ config('app.name', 'Tissue') }}</a>
        @if (Auth::guest())
            <ul class="right hide-on-med-and-down">
                <li><a href="{{ route('login') }}">ログイン</a></li>
            </ul>

            <ul id="nav-mobile" class="side-nav">
                <li><a href="{{ route('login') }}">ログイン</a></li>
            </ul>
        @else
            <ul class="right">
                <li><a class="waves-effect waves-light btn" href="{{ route('checkin') }}"><i class="material-icons left hide-on-med-and-down">create</i> チェックイン</a></li>
            </ul>
            <ul class="right hide-on-med-and-down">
                <li><a class="dropdown-button" data-activates="accountMenu" href="#">{{ Auth::user()->display_name }} さん<i class="material-icons right">arrow_drop_down</i></a></li>
            </ul>

            <ul id="nav-mobile" class="side-nav">
                <li><a href="#">{{ Auth::user()->display_name }} さん</a></li>
                <li><a href="{{ route('user.profile') }}">プロフィール</a></li>
                <li class="divider"></li>
                <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a></li>
            </ul>
        @endif
        <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
    </div>
</nav>
@yield('content')
<footer class="page-footer grey">
    <!--<div class="container"></div>-->
    <div class="footer-copyright">
        <div class="container">
            Copyright (c) 2017 shikorism.net
        </div>
    </div>
</footer>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="{{ asset('js/materialize.min.js') }}"></script>
<script>
    $(function(){
        $('.button-collapse').sideNav();
        $('.dropdown-button').dropdown();
        $('ul.tabs').tabs();
        @if (session('status'))
            Materialize.toast('{{ session("status") }}', 5000);
        @endif
    });
</script>
@yield('script')
</body>
</html>