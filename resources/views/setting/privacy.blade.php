@extends('setting.base')

@section('title', 'プライバシー設定')

@section('tab-content')
    <h3>プライバシー</h3>
    <hr>
    <form action="{{ route('setting.privacy.update') }}" method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <div class="custom-control custom-checkbox mb-2">
                <input id="protected" name="is_protected" class="custom-control-input" type="checkbox" {{ (old('is_protected') ?? Auth::user()->is_protected ) ? 'checked' : '' }}>
                <label class="custom-control-label" for="protected">全てのチェックイン履歴を非公開にする</label>
                <small class="form-text text-muted">プロフィール情報を除いて、全ての情報が非公開になります。</small>
            </div>
            <div class="custom-control custom-checkbox mb-2">
                <input id="private-likes" name="private_likes" class="custom-control-input" type="checkbox" {{ (old('private_likes') ?? Auth::user()->private_likes ) ? 'checked' : '' }}>
                <label class="custom-control-label" for="private-likes">いいねしたチェックイン一覧を非公開にする</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input id="accept-analytics" name="accept_analytics" class="custom-control-input" type="checkbox" {{ (old('accept_analytics') ?? Auth::user()->accept_analytics ) ? 'checked' : '' }}>
                <label class="custom-control-label" for="accept-analytics">匿名での統計にチェックインデータを利用することに同意します</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-2">更新</button>
    </form>
@endsection

@push('script')
    @vite('resources/assets/js/setting/privacy.ts')
@endpush
