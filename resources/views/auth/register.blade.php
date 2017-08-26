@extends('layouts.base')

@section('content')
<div class="container">
    <h2 class="grey-text">新規登録</h2>
    <div class="row">
        <form method="post" action="{{ route('register') }}" class="col s12 push-m3 m6">
            {{ csrf_field() }}

            <div class="row">
                <h6 class="grey-text col s12">ユーザー情報</h6>
                <div class="input-field col s12">
                    <i class="material-icons prefix">person</i>
                    <input id="name" name="name" class="validate{{ $errors->has('name') ? ' invalid' : '' }}" type="text" value="{{ old('name') }}" required>
                    <label for="name">ユーザー名</label>

                    @if ($errors->has('name'))
                        <span class="red-text"><strong>{{ $errors->first('name') }}</strong></span>
                    @endif
                </div>
                <div class="input-field col s12">
                    <i class="material-icons prefix">email</i>
                    <input id="email" name="email" class="validate{{ $errors->has('email') ? ' invalid' : '' }}" type="text" value="{{ old('email') }}" required>
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
                <div class="input-field col s12">
                    <i class="material-icons prefix"></i>
                    <input id="password-confirm" name="password_confirmation" class="validate" type="password" required>
                    <label for="password-confirm">パスワードの再入力</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <h6 class="grey-text">プライバシーに関するオプション (全て任意です)</h6>
                    <p>
                        <input id="protected" name="is_protected" class="filled-in" type="checkbox" {{ old('is_protected') ? 'checked' : '' }}>
                        <label for="protected">チェックイン履歴を非公開にする</label>
                    </p>
                    <p>
                        <input id="accept-analytics" name="accept_analytics" class="filled-in" type="checkbox" {{ old('accept_analytics') ? 'checked' : '' }}>
                        <label for="accept-analytics">匿名での統計にチェックインデータを利用することに同意します</label>
                    </p>
                </div>
            </div>
            <div class="row center">
                <div class="input-field col s12">
                    <button class="btn waves-effect waves-light teal lighten-2" type="submit">登録</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection