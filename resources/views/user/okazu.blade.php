@extends('user.base')

@section('title', $user->display_name . ' さんのオカズ')

@section('tab-content')
@if ($user->is_protected && !$user->isMe())
    <p class="mt-4">
        <i class="ti ti-lock"></i> このユーザはチェックイン履歴を公開していません。
    </p>
@else
@endif
@endsection
