@extends('user.base')

@section('title', $user->display_name . ' さんのオカズ')

@section('tab-content')
@if ($user->is_protected && !$user->isMe())
    <p class="mt-4">
        <span class="oi oi-lock-locked"></span> このユーザはチェックイン履歴を公開していません。
    </p>
@else
@endif
@endsection