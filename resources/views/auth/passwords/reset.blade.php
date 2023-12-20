@extends('layouts.base')

@section('title', 'パスワードの再発行')

@section('content')
<div class="container">
    <h2>パスワードの再発行</h2>
    <hr>
    <div class="row justify-content-center my-5">
        <div class="col-12 text-center">
            <p>新しいパスワードを入力し、<strong>パスワードを変更</strong>ボタンを押してください。</p>
        </div>
        <div class="col-lg-6">
            <form method="post" action="{{ route('password.request') }}">
                {{ csrf_field() }}

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email"><i class="ti ti-mail"></i> メールアドレス</label>
                    <input id="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" type="email" value="{{ old('email') }}" required>

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
                <div class="form-group">
                    <label for="password-confirm">パスワードの再入力</label>
                    <input id="password-confirm" name="password_confirmation" class="form-control" type="password" required>

                    @if ($errors->has('password_confirmation'))
                        <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
                    @endif
                </div>

                <button class="btn btn-primary" type="submit">パスワードを変更</button>
            </form>
        </div>
    </div>
</div>
@endsection
