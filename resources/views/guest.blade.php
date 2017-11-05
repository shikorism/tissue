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
    <div class="row">
        <div class="col-lg-4">
            <h1 class="text-center text-secondary display-3"><span class="oi oi-pencil"></span></h1>
            <h4 class="text-center my-4">記録</h4>
            <p>気持ちよかったその思い出を記録しましょう。楽しんだ時間や使ったオカズ、感想などを記録することができます。</p>
        </div>
        <div class="col-lg-4">
            <h1 class="text-center text-secondary display-3"><span class="oi oi-graph"></span></h1>
            <h4 class="text-center my-4">統計</h4>
            <p>記録を続けていくことで、ティッシュを使う頻度や時間の傾向、あるいはあなたのお気に入りのオカズが見えてくるようになります。我慢大会をするのも、猿を目指すのもまた一興。</p>
        </div>
        <div class="col-lg-4">
            <h1 class="text-center text-secondary display-3"><span class="oi oi-globe"></span></h1>
            <h4 class="text-center my-4">ソーシャル</h4>
            <p>ティッシュが蒸発するような人気のオカズや、底なしの体力を競い合うランキングなど、Webならではのサービスも用意<s class="grey-text">しています</s>したいですね。</p>
        </div>
    </div>
</div>
@endsection
