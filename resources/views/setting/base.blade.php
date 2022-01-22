@extends('layouts.base')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="list-group">
                    <div class="list-group-item disabled font-weight-bold">設定</div>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting' ? 'active' : '' }}"
                       href="{{ route('setting') }}"><span class="oi oi-person mr-1"></span> プロフィール</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.privacy' ? 'active' : '' }}"
                       href="{{ route('setting.privacy') }}"><span class="oi oi-shield mr-1"></span> プライバシー</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.filter.tags' ? 'active' : '' }}"
                       href="{{ route('setting.filter.tags') }}"><span class="oi oi-tags mr-1"></span> タグミュート</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.password' ? 'active' : '' }}"
                       href="{{ route('setting.password') }}"><span class="oi oi-key mr-1"></span> パスワードの変更</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.deactivate' ? 'active' : '' }}"
                       href="{{ route('setting.deactivate') }}"><span class="oi oi-trash mr-1"></span> アカウントの削除</a>
                </div>
                <div class="list-group mt-4">
                    <div class="list-group-item disabled font-weight-bold">アプリ連携</div>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.webhooks' ? 'active' : '' }}"
                       href="{{ route('setting.webhooks') }}"><span class="oi oi-link-intact mr-1"></span> Webhook</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.tokens' ? 'active' : '' }}"
                       href="{{ route('setting.tokens') }}"><span class="oi oi-key mr-1"></span> 個人用アクセストークン</a>
                </div>
                <div class="list-group mt-4">
                    <div class="list-group-item disabled font-weight-bold">データ管理</div>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.import' ? 'active' : '' }}"
                       href="{{ route('setting.import') }}"><span class="oi oi-data-transfer-upload mr-1"></span> データのインポート</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.export' ? 'active' : '' }}"
                       href="{{ route('setting.export') }}"><span class="oi oi-data-transfer-download mr-1"></span> データのエクスポート</a>
                </div>
            </div>
            <div class="tab-content col-lg-8">
                @yield('tab-content')
            </div>
        </div>
    </div>
@endsection
