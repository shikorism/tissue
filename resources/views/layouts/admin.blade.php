@extends('layouts.base')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="list-group mb-4">
                    <div class="list-group-item disabled font-weight-bold">管理</div>
                    <a class="list-group-item list-group-item-action {{ Route::is('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}"><i class="ti ti-dashboard mr-1"></i> ダッシュボード</a>
                    <a class="list-group-item list-group-item-action {{ Route::is('admin.info*') ? 'active' : '' }}"
                       href="{{ route('admin.info') }}"><i class="ti ti-speakerphone mr-1"></i> お知らせ</a>
                    <a class="list-group-item list-group-item-action {{ Route::is('admin.reports*') ? 'active' : '' }}"
                       href="{{ route('admin.reports') }}"><i class="ti ti-flag mr-1"></i> 通報</a>
                    <a class="list-group-item list-group-item-action {{ Route::is('admin.rule*') ? 'active' : '' }}"
                       href="{{ route('admin.rule') }}"><i class="ti ti-gavel mr-1"></i> 通報理由</a>
                </div>
            </div>
            <div class="tab-content col-lg-9">
                @yield('tab-content')
            </div>
        </div>
    </div>
@endsection
