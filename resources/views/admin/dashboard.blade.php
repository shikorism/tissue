@extends('layouts.admin')

@section('title', 'ダッシュボード')

@section('tab-content')
    <div class="container d-flex flex-column align-items-center">
        <img src="{{ asset('dashboard.png') }}" class="w-50"/>
        <p class="text-muted">TODO: 役に立つ情報を表示する</p>
    </div>
@endsection