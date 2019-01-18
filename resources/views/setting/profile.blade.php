@extends('setting.base')

@section('title', 'プロフィール設定')

@section('tab-content')
    <h3>プロフィール</h3>
    <hr>
    <form action="{{ route('setting.profile.update') }}" method="post">
        {{ csrf_field() }}
        <div class="from-group">
            <label for="display_name">名前</label>
            <input id="display_name" name="display_name" type="text" class="form-control {{ $errors->has('display_name') ? ' is-invalid' : '' }}"
                   value="{{ old('display_name') ?? Auth::user()->display_name }}" maxlength="20" autocomplete="off">

            @if ($errors->has('display_name'))
                <div class="invalid-feedback">{{ $errors->first('display_name') }}</div>
            @endif
        </div>
        <div class="from-group mt-2">
            <label for="name">ユーザー名</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">@</div>
                </div>
                <input id="name" name="name" type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
            </div>
            <small class="form-text text-muted">現在は変更できません。</small>
        </div>

        <button type="submit" class="btn btn-primary mt-4">更新</button>
    </form>
@endsection
