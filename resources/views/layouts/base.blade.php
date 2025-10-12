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
    @vite('resources/assets/sass/app.scss')
    @viteReactRefresh

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
        @if (!App::isDownForMaintenance())
            @guest
                <div class="navbar-nav flex-row">
                    <ul class="navbar-nav ml-auto mr-2">
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="nav-link">会員登録</a>
                        </li>
                    </ul>
                    <form class="form-inline">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">ログイン</a>
                    </form>
                </div>
            @endguest
        @endif
    </div>
</nav>
@if (session('status'))
<div class="container tis-status-container">
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
        <p>Copyright (c) 2017-2024 shikorism.net</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="https://github.com/shikorism/tissue" class="text-dark">GitHub</a></li>
            <li class="list-inline-item"><a href="{{ url('/apidoc.html') }}" class="text-dark">API</a></li>
        </ul>
    </div>
</footer>
<div class="toast tis-toast">
    <div class="toast-body"></div>
</div>
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
@vite('resources/assets/js/app.ts')
@stack('script')
</body>
</html>
