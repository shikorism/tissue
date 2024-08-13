@extends('layouts.base')

@section('title', 'アカウント削除完了')

@section('content')
    <div class="container">
        <h3>アカウントを削除しました</h3>
        <hr>
        <p>Tissueをご利用いただき、ありがとうございました。</p>
        <p class="my-5 text-center"><a class="btn btn-link" href="{{ route('home') }}">トップページへ</a></p>
    </div>
@endsection

@push('script')
    @vite('resources/assets/js/setting/deactivate.ts')
@endpush
