@extends('layouts.base')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="list-group">
                    <div class="list-group-item disabled font-weight-bold">設定</div>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting' ? 'active' : '' }}"
                       href="{{ route('setting') }}"><i class="ti ti-user mr-1"></i> プロフィール</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.privacy' ? 'active' : '' }}"
                       href="{{ route('setting.privacy') }}"><i class="ti ti-shield-lock mr-1"></i> プライバシー</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.filter.tags' ? 'active' : '' }}"
                       href="{{ route('setting.filter.tags') }}"><i class="ti ti-tag-off mr-1"></i> タグミュート</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.password' ? 'active' : '' }}"
                       href="{{ route('setting.password') }}"><i class="ti ti-password mr-1"></i> パスワードの変更</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.deactivate' ? 'active' : '' }}"
                       href="{{ route('setting.deactivate') }}"><i class="ti ti-trash mr-1"></i> アカウントの削除</a>
                </div>
                <div class="list-group mt-4">
                    <div class="list-group-item disabled font-weight-bold">アプリ連携</div>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.webhooks' ? 'active' : '' }}"
                       href="{{ route('setting.webhooks') }}"><i class="ti ti-webhook mr-1"></i> Webhook</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.tokens' ? 'active' : '' }}"
                       href="{{ route('setting.tokens') }}"><i class="ti ti-key mr-1"></i> 個人用アクセストークン</a>
                </div>
                <div class="list-group mt-4">
                    <div class="list-group-item disabled font-weight-bold">データ管理</div>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.import' ? 'active' : '' }}"
                       href="{{ route('setting.import') }}"><i class="ti ti-upload mr-1"></i> データのインポート</a>
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.export' ? 'active' : '' }}"
                       href="{{ route('setting.export') }}"><i class="ti ti-download mr-1"></i> データのエクスポート</a>
                </div>
            </div>
            <div class="tab-content col-lg-8">
                @yield('tab-content')
            </div>
        </div>
    </div>
@endsection
