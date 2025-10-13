<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Tissue') }}</title>
    <link href="{{ asset('manifest.json') }}" rel="manifest">
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
<div id="app"></div>
@guest
    @component('components.agecheck')
    @endcomponent
@endguest
@vite('frontend/App.tsx')
@vite('resources/assets/js/agecheck.ts')
</body>
