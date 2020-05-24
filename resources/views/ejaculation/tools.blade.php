@extends('layouts.base')

@section('title', 'ブックマークレットについて')

@section('content')
<div class="container">
    <h2>ブックマークレットと共有機能について</h2>
    <hr>
    <div class="row">
        <div class="col-lg-10">
            <p>以下のブックマークレットを使うと、ブラウザで現在見ているページで簡単にチェックインすることができます。</p>
            <div class="card mb-4">
                <div class="card-body">
                    <pre class="mb-0"><code>javascript:location.href='{{ url('/') }}/checkin?link='+encodeURIComponent(location.href)</code></pre>
                </div>
            </div>
            <p>また、<a href="https://www.chromestatus.com/feature/5662315307335680">Web Share Target</a> に対応しているブラウザでは、他のWebサイトやアプリからURLを「共有」することができます。</p>
            <ul>
                <li>Android版 Google Chrome の場合
                    <ul>
                        <li>画面下に出てくる「ホーム画面に Tissue を追加」、もしくは右上のメニューからインストール</li>
                        <li>任意のアプリからURLを共有 → Tissue を選択 → チェックイン画面</li>
                    </ul>
                </li>
            </ul>
            <p>※ Web Share Target の仕様はまだドラフト段階で、今後仕様の変更により動かなくなる場合があります。</p>
        </div>
    </div>
    <h2 class="mt-4">高度な使い方</h2>
    <hr>
    <div class="row">
        <div class="col-lg-10">
            <p>チェックイン画面のURLにクエリパラメータを付加することで、各フィールドに値をセットした状態で開くことができます。</p>
            <p>例: <a href="{{ url('checkin?date=1900/01/01&time=00:00&tags=blah+blur&link=hoge&note=piyo') }}">{{ url('checkin?date=1900/01/01&time=00:00&tags=blah+blur&link=hoge&note=piyo') }}</a></p>
        </div>
    </div>
</div>
@endsection
