@extends('layouts.admin')

@section('title', 'ダッシュボード')

@section('tab-content')
    <div class="container">
        <div class="d-flex flex-column align-items-center">
            <img src="{{ asset('dashboard.png') }}" class="w-50"/>
        </div>
        <div class="row">
            <div class="card col-12 col-md-6 offset-md-3">
                <div class="card-body">
                    <h5 class="card-title">サーバー情報</h5>
                    <div class="d-flex justify-content-between">
                        <p class="my-0">PHP</p>
                        <p class="my-0">{{ PHP_VERSION }}</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="my-0">PostgreSQL</p>
                        <p class="my-0">{{ $pgVersion }}</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="my-0">Laravel</p>
                        <p class="my-0">{{ app()->version() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
