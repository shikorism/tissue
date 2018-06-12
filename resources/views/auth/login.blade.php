@extends('layouts.base')

@section('content')
<div class="container">
    <h2>ログイン</h2>
    <hr>
    <div class="row justify-content-center my-5">
        <div class="col-lg-6">
            <form method="post" action="{{ route('login') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="email"><span class="oi oi-envelope-closed"></span> メールアドレス</label>
                    <input id="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" type="text" value="{{ old('email') }}" required autofocus>

                    @if ($errors->has('email'))
                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="password"><span class="oi oi-key"></span> パスワード</label>
                    <input id="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" required>

                    @if ($errors->has('password'))
                        <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                    @endif
                </div>
                <div class="form-check">
                    <label class="custom-control custom-checkbox">
                        <input id="remember" name="remember" class="custom-control-input" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">保存する</span>
                    </label>
                </div>

                <button class="btn btn-primary" type="submit">ログイン</button>
                <a href="{{ route('password.request') }}" class="btn btn-link">パスワードを忘れた場合はこちら</a>
            </form>
        </div>
    </div>
</div>
@endsection