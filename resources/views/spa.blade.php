<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Tissue') }}</title>
    <link href="{{ asset('manifest.json') }}" rel="manifest">
    <style>
        .tis-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .tis-loading div {
            width: 64px;
            height: 64px;
            background: url("{{ asset('apple-touch-icon.png') }}") no-repeat center center;
            background-size: contain;
            filter: grayscale(100%);
            opacity: 0.5;
            animation: tis-loading 1s infinite alternate;
        }
        @keyframes tis-loading {
            0% { opacity: 0.3; }
            100% { opacity: 0.5; }
        }
    </style>
    @vite('resources/assets/sass/agecheck.css')
    @viteReactRefresh
</head>
<body class="{{Auth::check() ? '' : 'tis-need-agecheck'}}">
<noscript>
    <p>Tissueを利用するには、ブラウザのJavaScriptとCookieを有効にする必要があります。</p>
    <p>
        <a href="https://www.enable-javascript.com/ja/" target="_blank" rel="nofollow noopener">ブラウザでJavaScriptを有効にする方法</a>
        ･ <a href="https://www.whatismybrowser.com/guides/how-to-enable-cookies/auto" target="_blank" rel="nofollow noopener">ブラウザでCookieを有効にする方法</a>
    </p>
</noscript>
<div id="app">
    {{-- App.tsxのInitialLoadingと同じ --}}
    <div class="tis-loading" aria-busy="true">
        <div></div>
    </div>
</div>
@guest
    @component('components.agecheck')
    @endcomponent
@endguest
<script>
    window.APP_META = @js([
        'supportLink' => config('app.support_link')
    ])
</script>
@vite('frontend/index.tsx')
@vite('resources/assets/js/agecheck.ts')
</body>
</html>
