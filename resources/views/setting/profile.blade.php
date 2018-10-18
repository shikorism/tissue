@extends('setting.base')

@section('tab-content')
    <h3>プロフィール</h3>
    <hr>
    <form action="" method="post">
        {{ csrf_field() }}
        <div class="from-group">
            <label for="display_name">名前</label>
            <input id="display_name" name="display_name" type="text" class="form-control">
        </div>
        <div class="from-group mt-2">
            <label for="name">ユーザー名</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">@</div>
                </div>
                <input id="name" name="name" type="text" class="form-control" disabled>
            </div>
            <small class="form-text text-muted">現在は変更できません。</small>
        </div>

        <button type="submit" class="btn btn-primary mt-4">更新</button>
    </form>

    <h3 class="mt-5">プライバシー</h3>
    <hr>
    <form action="" method="post">
        {{ csrf_field() }}
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

        <button type="submit" class="btn btn-primary mt-2">更新</button>
    </form>
@endsection