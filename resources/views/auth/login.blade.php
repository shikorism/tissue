@extends('layouts.base')

@section('title', 'ログイン')

@section('content')
<div class="container">
    <h2>ログイン</h2>
    <hr>
    <div class="row justify-content-center my-5">
        <div class="col-lg-6">
            <form method="post" action="{{ route('login') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="email"><i class="ti ti-mail"></i> メールアドレス</label>
                    <input id="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" type="email" value="{{ old('email') }}" required autofocus>

                    @if ($errors->has('email'))
                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="password"><i class="ti ti-key"></i> パスワード</label>
                    <input id="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" required>

                    @if ($errors->has('password'))
                        <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                    @endif
                </div>
                <div class="custom-control custom-checkbox mb-3">
                    <input id="remember" name="remember" class="custom-control-input" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="remember">保存する</label>
                </div>

                <button class="btn btn-primary" type="submit">ログイン</button>
                <a href="{{ route('password.request') }}" class="btn btn-link">パスワードを忘れた場合はこちら</a>
            </form>
        </div>
    </div>
</div>
@endsection
