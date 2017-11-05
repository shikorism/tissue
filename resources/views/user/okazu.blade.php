@extends('user.base')

@section('tab-content')
@if ($user->is_protected && !(Auth::check() && $user->id === Auth::user()->id))
    <p class="mt-4">
        <span class="oi oi-lock-locked"></span> このユーザはチェックイン履歴を公開していません。
    </p>
@else
@endif
@endsection