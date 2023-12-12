@extends('layouts.base')

@section('title', '新規登録')

@push('head')
    @if (!empty(config('captcha.secret')))
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush

@section('content')
<div class="container">
    <h2>新規登録</h2>
    <hr>
    <div class="alert alert-warning">
        <p class="mb-0"><strong>注意！</strong> Tissueでは、登録に使用したメールアドレスの <a href="https://ja.gravatar.com/" rel="noreferrer">Gravatar</a> を使用します。</p>
        <p class="mb-0">他の場所での活動と紐付いてほしくない場合、使用予定のメールアドレスにGravatarが設定されていないかを確認することを推奨します。</p>
    </div>
    <div class="row justify-content-center my-5">
        <div class="col-lg-6">
            <form method="post" action="{{ route('register') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="name"><i class="ti ti-user"></i> ユーザー名</label>
                    <input id="name" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" type="text" value="{{ old('name') }}" required>
                    <small class="form-text text-muted">半角英数字と一部記号が使用できます。一度決めたら変更できません。</small>

                    @if ($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                    @endif
                </div>
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
                </div>
                <div class="form-row ml-1 mt-4">
                    <h6 class="mb-3">プライバシーに関するオプション (全て任意です)</h6>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox mb-2">
                            <input id="protected" name="is_protected" class="custom-control-input" type="checkbox" {{ old('is_protected') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="protected">全てのチェックイン履歴を非公開にする</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input id="accept-analytics" name="accept_analytics" class="custom-control-input" type="checkbox" {{ old('accept_analytics') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="accept-analytics">匿名での統計にチェックインデータを利用することに同意します</label>
                        </div>
                    </div>
                </div>
                @if (!empty(config('captcha.secret')))
                    <div class="form-row ml-1 mt-2 my-4">
                        <div class="mx-auto">
                            {!! NoCaptcha::display() !!}
                        </div>
                        @if ($errors->has('g-recaptcha-response'))
                            <div class="invalid-feedback d-block text-center">{{ $errors->first('g-recaptcha-response') }}</div>
                        @endif
                    </div>
                @endif

                <div class="text-center">
                    <button class="btn btn-primary btn-lg" type="submit">登録</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
