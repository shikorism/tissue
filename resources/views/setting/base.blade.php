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
                    <a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.deactivate' ? 'active' : '' }}"
                       href="{{ route('setting.deactivate') }}"><span class="oi oi-trash mr-1"></span> アカウントの削除</a>
                    {{--<a class="list-group-item list-group-item-action {{ Route::currentRouteName() === 'setting.password' ? 'active' : '' }}"
                       href="{{ route('setting.password') }}"><span class="oi oi-key mr-1"></span> パスワード</a>--}}
                </div>
            </div>
            <div class="tab-content col-lg-8">
                @yield('tab-content')
            </div>
        </div>
    </div>
@endsection
