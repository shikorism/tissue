@extends('layouts.base-old')

@section('content')
<div class="container">
    <h2 class="grey-text">ログイン</h2>
    <div class="row">
        <form method="post" action="{{ route('login') }}" class="col s12 push-m3 m6">
            {{ csrf_field() }}

            <div class="row">
                <div class="input-field col s12">
                    <i class="material-icons prefix">email</i>
                    <input id="email" name="email" class="validate{{ $errors->has('email') ? ' invalid' : '' }}" type="text" value="{{ old('email') }}" required autofocus>
                    <label for="email">メールアドレス</label>

                    @if ($errors->has('email'))
                        <span class="red-text"><strong>{{ $errors->first('email') }}</strong></span>
                    @endif
                </div>
                <div class="input-field col s12">
                    <i class="material-icons prefix">lock</i>
                    <input id="password" name="password" class="validate{{ $errors->has('password') ? ' invalid' : '' }}" type="password" required>
                    <label for="password">パスワード</label>

                    @if ($errors->has('password'))
                        <span class="red-text"><strong>{{ $errors->first('password') }}</strong></span>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <p>
                        <input id="remember" name="remember" class="filled-in" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">保存する</label>
                    </p>
                </div>
            </div>
            <div class="row center">
                <div class="input-field col s12">
                    <button class="btn waves-effect waves-light teal lighten-2" type="submit">ログイン</button>
                </div>
                <div class="input-field col s12">
                    <a href="{{ route('password.request') }}">パスワードを忘れた場合はこちら</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection