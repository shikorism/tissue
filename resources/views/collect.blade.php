@extends('layouts.base')

@section('title', 'コレクションに追加')

@section('content')
<div class="container">
    <h2>コレクションに追加</h2>
    <hr>
    <div class="row justify-content-center mt-5">
        <div id="form" class="col-lg-6">
            <div class="text-center small" style="height: 640px;">しばらくお待ちください…</div>
        </div>
    </div>
</div>
@endsection

@push('script')
    @vite('resources/assets/js/collect.tsx')
@endpush
