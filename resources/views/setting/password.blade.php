@extends('setting.base')

@section('title', 'パスワードの変更')

@section('tab-content')
    <h3>パスワードの変更</h3>
    <hr>
    <form action="{{ route('setting.password.update') }}" method="post">
        {{ csrf_field() }}

        <div class="form-group">
            <label for="current_password">現在のパスワード</label>
            <input id="current_password" name="current_password" class="form-control{{ $errors->has('current_password') ? ' is-invalid' : '' }}" type="password" required>

            @if ($errors->has('current_password'))
                <div class="invalid-feedback">{{ $errors->first('current_password') }}</div>
            @endif
        </div>
        <div class="form-group">
            <label for="password">新しいパスワード</label>
            <input id="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" required>

            @if ($errors->has('password'))
                <div class="invalid-feedback">{{ $errors->first('password') }}</div>
            @endif
        </div>
        <div class="form-group">
            <label for="password_confirmation">新しいパスワード (確認)</label>
            <input id="password_confirmation" name="password_confirmation" class="form-control" type="password" required>

            @if ($errors->has('password_confirmation'))
                <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary mt-4">変更</button>
    </form>
@endsection
