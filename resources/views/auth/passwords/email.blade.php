@extends('layouts.base')

@section('title', 'パスワードの再発行')

@section('content')
<div class="container">
    <h2>パスワードの再発行</h2>
    <hr>
    <div class="row justify-content-center my-5">
        <div class="col-12 text-center">
            <p>本サイトでお使いのメールアドレスを入力して、<strong>パスワードを再発行</strong>ボタンを押してください。<br>入力されたメールアドレスに、手続きを行うためのリンクが書かれたメールが送信されます。</p>
        </div>
        <div class="col-lg-6">
            <form method="post" action="{{ route('password.email') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="email"><i class="ti ti-mail"></i> メールアドレス</label>
                    <input id="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" type="email" value="{{ old('email') }}" required autofocus>

                    @if ($errors->has('email'))
                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                    @endif
                </div>

                <button class="btn btn-primary" type="submit">パスワードを再発行</button>
            </form>
        </div>
    </div>
</div>
@endsection
