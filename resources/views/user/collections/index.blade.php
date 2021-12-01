@extends('user.base')

@section('title', $user->display_name . ' (@' . $user->name . ')')

@push('head')
@endpush

@section('sidebar')
@endsection

@section('tab-content')
@if ($user->is_protected && !$user->isMe())
    <p class="mt-4">
        <span class="oi oi-lock-locked"></span> このユーザはチェックイン履歴を公開していません。
    </p>
@elseif (empty($collections))
    <p class="mt-4">
        コレクションがありません。
    </p>
@endif
@endsection

@push('script')
@endpush

