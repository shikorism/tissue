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
    <script src="{{ mix('js/user/collections.js') }}"></script>
@endpush

