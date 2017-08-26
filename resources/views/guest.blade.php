@extends('layouts.base')

@section('content')
<div class="section no-pad-bot" id="index-banner">
    <div class="container">
        <br><br>
        <h1 class="header center grey-text">{{ config('app.name', 'Tissue') }}</h1>
        <div class="row center">
            <h5 class="header col s12 light">気持ちよくティッシュを使った、そのあとの感想戦。</h5>
            <p class="col s12">あるいは遺伝子の墓場</p>
        </div>
        <div class="row center">
            <a href="{{ url('/register')  }}" class="btn-large waves-effect waves-light teal lighten-2">今すぐ登録</a>
        </div>
        <br><br>

    </div>
</div>
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m4">
                <div class="icon-block">
                    <h2 class="center teal-text"><i class="large material-icons">mode_edit</i></h2>
                    <h5 class="center">記録</h5>
                    <p class="light">気持ちよかったその思い出を記録しましょう。楽しんだ時間や使ったオカズ、感想などを記録することができます。</p>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="icon-block">
                    <h2 class="center teal-text"><i class="large material-icons">show_chart</i></h2>
                    <h5 class="center">統計</h5>
                    <p class="light">記録を続けていくことで、ティッシュを使う頻度や時間の傾向、あるいはあなたのお気に入りのオカズが見えてくるようになります。我慢大会をするのも、猿を目指すのもまた一興。</p>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="icon-block">
                    <h2 class="center teal-text"><i class="large material-icons">public</i></h2>
                    <h5 class="center">ソーシャル</h5>
                    <p class="light">ティッシュが蒸発するような人気のオカズや、底なしの体力を競い合うランキングなど、Webならではのサービスも用意<s class="grey-text">しています</s>したいですね。</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
