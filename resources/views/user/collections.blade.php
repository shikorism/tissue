@extends('layouts.base')

@section('title', $user->display_name . ' さんのコレクション')

@push('head')
@endpush

@section('content')
    @component('user.components.header', ['user' => $user])
    @endcomponent
    <div id="app"></div>
@endsection

@push('script')
    @vite('resources/assets/js/user/collections.tsx')
@endpush

