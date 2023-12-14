@extends('layouts.base')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container text-center">
        <h1 class="display-3">{{ config('app.name', 'Tissue') }}</h1>
        <p class="lead mb-2">気持ちよくティッシュを使った、そのあとの感想戦。</p>
        <p class="text-secondary">あるいは遺伝子の墓場</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg mt-4" role="button">今すぐ登録</a>
    </div>
</div>
<div class="container">
    <div class="row py-4">
        <div class="col-lg-4">
            <h1 class="text-center text-secondary display-1"><span class="ti ti-ballpen"></span></h1>
        </div>
        <div class="col-lg-8">
            <h4 class="text-center text-lg-left mb-4">思い出を残す</h4>
            <p>気持ちよかったその思い出を記録しましょう。楽しんだ時間や使ったオカズ、感想などを記録することができます。</p>
        </div>
    </div>
    <div class="row py-4">
        <div class="col-lg-4">
            <h1 class="text-center text-secondary display-1"><i class="ti ti-timeline"></i></h1>
        </div>
        <div class="col-lg-8">
            <h4 class="text-center text-lg-left mb-4">自分を知る</h4>
            <p>記録を続けていくことで、ティッシュを使う頻度や時間の傾向、あるいはあなたのお気に入りのジャンルが見えてくるようになります。</p>
        </div>
    </div>
    <h4 class="mt-5 mb-4">{{ config('app.name', 'Tissue') }} とは？</h4>
    <p>{{ config('app.name', 'Tissue') }} は自慰の記録に特化したライフログサービスです。</p>
    <p>あなたが自慰をした時刻や使ったオカズを記録 (チェックイン) し、後から見返すことができます。頻度や傾向を自動的に集計し、可視化する機能も搭載しています。</p>
    <p>また、他の利用者が公開しているチェックインからオカズを探したり、ちょっとしたソーシャル機能としていいねを付けたりすることもできます。</p>
    <h4 class="mt-5 mb-4">お知らせ</h4>
    <div class="list-group list-group-flush">
        @foreach($informations as $info)
            <a class="list-group-item" href="{{ route('info.show', ['id' => $info->id]) }}">
            @if ($info->pinned)
                <span class="badge badge-secondary"><i class="ti ti-pinned-filled"></i>ピン留め</span>
            @endif
                <span class="badge {{ $categories[$info->category]['class'] }}">{{ $categories[$info->category]['label'] }}</span> {{ $info->title }} <small class="text-secondary">- {{ $info->created_at->format('n月j日') }}</small>
            </a>
        @endforeach
        <a href="{{ route('info') }}" class="list-group-item text-right">お知らせ一覧 &raquo;</a>
    </div>
</div>
@endsection
