@extends('layouts.base')

@section('content')
<div class="container">
    <h2>新規登録</h2>
    <hr>
    <div class="row justify-content-center my-5">
        <div class="col-lg-6">
            <form method="post" action="{{ route('register') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="name"><span class="oi oi-person"></span> ユーザー名</label>
                    <input id="name" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" type="text" value="{{ old('name') }}" required>

                    @if ($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="email"><span class="oi oi-envelope-closed"></span> メールアドレス</label>
                    <input id="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" type="text" value="{{ old('email') }}" required>

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
                <div class="form-group">
                    <label for="password-confirm">パスワードの再入力</label>
                    <input id="password-confirm" name="password_confirmation" class="form-control" type="password" required>
                </div>
                <div class="form-row ml-1 mt-4">
                    <h6 class="mb-3">プライバシーに関するオプション (全て任意です)</h6>

                    <div class="form-group">
                        <div class="form-check">
                            <label class="custom-control custom-checkbox">
                                <input id="protected" name="is_protected" class="custom-control-input" type="checkbox" {{ old('is_protected') ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">全てのチェックイン履歴を非公開にする</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="custom-control custom-checkbox">
                                <input id="accept-analytics" name="accept_analytics" class="custom-control-input" type="checkbox" {{ old('accept_analytics') ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">匿名での統計にチェックインデータを利用することに同意します</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button class="btn btn-primary btn-lg" type="submit">登録</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection