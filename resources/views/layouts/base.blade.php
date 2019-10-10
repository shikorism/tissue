<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @hasSection('title')
        <title>@yield('title') - {{ config('app.name', 'Tissue') }}</title>
    @else
        <title>{{ config('app.name', 'Tissue') }}</title>
    @endif

    <link href="{{ asset('manifest.json') }}" rel="manifest">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    @stack('head')
</head>
<body class="{{Auth::check() ? '' : 'tis-need-agecheck'}}">
<noscript class="navbar navbar-light bg-warning">
    <div class="container-fluid">
        <div class="d-flex flex-column mx-auto">
            <p class="m-0 text-dark">Tissueを利用するには、ブラウザのJavaScriptとCookieを有効にする必要があります。</p>
            <p class="m-0 text-info">
                <a href="https://www.enable-javascript.com/ja/" target="_blank" rel="nofollow noopener">ブラウザでJavaScriptを有効にする方法</a>
                ･ <a href="https://www.whatismybrowser.com/guides/how-to-enable-cookies/auto" target="_blank" rel="nofollow noopener">ブラウザでCookieを有効にする方法</a>
            </p>
        </div>
    </div>
</noscript>
<nav class="navbar navbar-expand-lg navbar-light bg-light {{ !Auth::check() && Route::currentRouteName() === 'home' ? '' : 'mb-4'}}">
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>

    <div class="container">
        <a href="{{ route('home') }}" class="navbar-brand mr-auto">{{ config('app.name', 'Tissue') }}</a>
        @auth
            <div class="d-lg-none navbar-nav">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle p-2" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ Auth::user()->getProfileImageUrl(30) }}" width="30" height="30" class="rounded d-inline-block align-top">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right position-absolute" aria-labelledby="navbarDropdownMenuLink" id="navbarAccountDropdownSp">
                        <a href="{{ route('user.profile', ['name' => Auth::user()->name]) }}" class="dropdown-item text-truncate">
                            <strong>{{ Auth::user()->display_name }}</strong>
                            <p class="mb-0 text-muted">
                                <span>&commat;{{ Auth::user()->name }}</span>
                            </p>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('user.profile', ['name' => Auth::user()->name]) }}" class="dropdown-item">プロフィール</a>
                        <a href="{{ route('user.likes', ['name' => Auth::user()->name]) }}" class="dropdown-item">いいね</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('setting') }}" class="dropdown-item">設定</a>
                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
                    </div>
                </div>
            </div>
        @endauth
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            @auth
                <!-- PC navbar -->
                <div class="d-none d-lg-flex navbar-collapse ml-3">
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
                        <li class="nav-item {{ stripos(Route::currentRouteName(), 'tag') === 0 ? 'active' : ''}}">
                            <a class="nav-link" href="{{ route('tag') }}">タグ一覧</a>
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
                        <a href="{{ route('checkin') }}" class="btn btn-outline-primary">チェックイン</a>
                    </form>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{ Auth::user()->getProfileImageUrl(30) }}" width="30" height="30" class="rounded d-inline-block align-top">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                <a href="{{ route('user.profile', ['name' => Auth::user()->name]) }}" class="dropdown-item">
                                    <strong>{{ Auth::user()->display_name }}</strong>
                                    <p class="mb-0 text-muted">
                                        <span>&commat;{{ Auth::user()->name }}</span>
                                    </p>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('user.profile', ['name' => Auth::user()->name]) }}" class="dropdown-item">プロフィール</a>
                                <a href="{{ route('user.likes', ['name' => Auth::user()->name]) }}" class="dropdown-item">いいね</a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('setting') }}" class="dropdown-item">設定</a>
                                @can ('admin')
                                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">管理</a>
                                @endcan
                                <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- SP navbar -->
                <div class="d-lg-none">
                    <div class="row mt-2">
                        <div class="col">
                            <a class="btn btn-{{ stripos(Route::currentRouteName(), 'home') === 0 ? 'primary' : 'outline-secondary'}}" href="{{ route('home') }}" role="button">ホーム</a>
                        </div>
                        <div class="col">
                            <a class="btn btn-{{ stripos(Route::currentRouteName(), 'user.profile') === 0 ? 'primary' : 'outline-secondary'}}" href="{{ route('user.profile', ['name' => Auth::user()->name]) }}" role="button">タイムライン</a>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <a class="btn btn-{{ stripos(Route::currentRouteName(), 'user.stats') === 0 ? 'primary' : 'outline-secondary'}}" href="{{ route('user.stats', ['name' => Auth::user()->name]) }}" role="button">グラフ</a>
                        </div>
                        <div class="col">
                            <a class="btn btn-{{ stripos(Route::currentRouteName(), 'user.okazu') === 0 ? 'primary' : 'outline-secondary'}}" href="{{ route('user.okazu', ['name' => Auth::user()->name]) }}" role="button">オカズ</a>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <a class="btn btn-{{ stripos(Route::currentRouteName(), 'tag') === 0 ? 'primary' : 'outline-secondary'}}" href="{{ route('tag') }}" role="button">タグ一覧</a>
                        </div>
                        <div class="col">
                        </div>
                    </div>
                    {{-- <div class="row mt-2">
                        <div class="col">
                            <a class="btn btn-outline-secondary" href="{{ route('ranking') }}">ランキング</a>
                        </div>
                    </div> --}}
                    <div class="row mt-2">
                        <form action="{{ stripos(Route::currentRouteName(), 'search') === 0 ? route(Route::currentRouteName()) : route('search') }}" class="col">
                            <div class="input-group">
                                <input type="search" name="q" class="form-control" placeholder="検索..." value="{{ stripos(Route::currentRouteName(), 'search') === 0 ? $inputs['q'] : '' }}" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit"><span class="oi oi-magnifying-glass" aria-hidden="true"></span><span class="sr-only">検索</span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row mt-2">
                        <form class="form-inline col">
                            <a class="btn btn-outline-primary" href="{{ route('checkin') }}">チェックイン</a>
                        </form>
                    </div>
                </div>
            @endauth
            @guest
                <!-- PC navbar -->
                <div class="d-none d-lg-flex navbar-collapse">
                    <ul class="navbar-nav ml-auto mr-2">
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="nav-link">会員登録</a>
                        </li>
                    </ul>
                    <form class="form-inline">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">ログイン</a>
                    </form>
                </div>
                <!-- SP navbar -->
                <div class="d-lg-none">
                    <div class="row mt-2">
                        <div class="col">
                            <a class="btn btn-outline-secondary" href="{{ route('register') }}" role="button">会員登録</a>
                        </div>
                        <div class="col">
                            <form class="form-inline">
                                <a class="btn btn-outline-secondary" href="{{ route('login') }}">ログイン</a>
                            </form>
                        </div>
                    </div>
                </div>
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
        <p>Copyright (c) 2017-2019 shikorism.net</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="https://github.com/shibafu528" class="text-dark">Admin(@shibafu528)</a></li>
            <li class="list-inline-item"><a href="https://github.com/shikorism/tissue" class="text-dark">GitHub</a></li>
        </ul>
    </div>
</footer>
@guest
<div class="modal fade" id="ageCheckModal" tabindex="-1" role="dialog" aria-labelledby="ageCheckModalTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ageCheckModalTitle">Tissue へようこそ！</h5>
      </div>
      <div class="modal-body">
        この先のコンテンツには暴力表現や性描写など、18歳未満の方が閲覧できないコンテンツが含まれています。
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">まかせて</button>
        <a href="https://cookpad.com" rel="noreferrer" class="btn btn-secondary">ごめん無理</a>
      </div>
    </div>
  </div>
</div>
@endguest
<script src="{{ mix('js/manifest.js') }}"></script>
<script src="{{ mix('js/vendor.js') }}"></script>
<script src="{{ mix('js/app.js') }}"></script>
@stack('script')
</body>
</html>
