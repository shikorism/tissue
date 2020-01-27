@extends('setting.base')

@section('title', 'プロフィール設定')

@section('tab-content')
    <h3>プロフィール</h3>
    <hr>
    <form action="{{ route('setting.profile.update') }}" method="post">
        {{ csrf_field() }}
        <div class="from-group">
            <label for="name">アイコン</label>
            <img src="{{ Auth::user()->getProfileImageUrl(128) }}" srcset="{{ Formatter::profileImageSrcSet(Auth::user(), 128) }}" class="rounded d-block">
            <small class="form-text text-muted">変更は<a href="https://gravatar.com/" target="_blank">Gravatar</a>から行えます。</small>
        </div>
        <div class="from-group mt-3">
            <label for="display_name">名前</label>
            <input id="display_name" name="display_name" type="text" class="form-control {{ $errors->has('display_name') ? ' is-invalid' : '' }}"
                   value="{{ old('display_name') ?? Auth::user()->display_name }}" maxlength="20" autocomplete="off">

            @if ($errors->has('display_name'))
                <div class="invalid-feedback">{{ $errors->first('display_name') }}</div>
            @endif
        </div>
        <div class="from-group mt-3">
            <label for="name">ユーザー名</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">@</div>
                </div>
                <input id="name" name="name" type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
            </div>
            <small class="form-text text-muted">変更することはできません。</small>
        </div>
        <div class="from-group mt-3">
            <label for="email">メールアドレス</label>
            <input id="email" name="email" type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') ?? Auth::user()->email }}">

            @if ($errors->has('email'))
                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
            @endif
        </div>
        <div class="form-group mt-3">
            <label for="bio">自己紹介</label>
            <textarea id="bio" name="bio" rows="3" class="form-control {{ $errors->has('bio') ? ' is-invalid' : '' }}">{{ old('bio') ?? Auth::user()->bio }}</textarea>
            <small class="form-text text-muted">最大 160 文字</small>

            @if ($errors->has('bio'))
                <div class="invalid-feedback">{{ $errors->first('bio') }}</div>
            @endif
        </div>
        <div class="form-group mt-3">
            <label for="url">URL</label>
            <input id="url" name="url" type="url" class="form-control {{ $errors->has('url') ? ' is-invalid' : '' }}"
                   value="{{ old('url') ?? Auth::user()->url }}" autocomplete="off">

            @if ($errors->has('url'))
                <div class="invalid-feedback">{{ $errors->first('url') }}</div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary mt-4">更新</button>
    </form>
@endsection
