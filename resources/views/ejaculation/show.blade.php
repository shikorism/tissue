@extends('layouts.base')

@if (!$user->isMe() && ($user->is_protected || $ejaculation->is_private))
    @section('title', $user->display_name . ' さんのチェックイン')
@else
    @section('title', $user->display_name . ' さんのチェックイン (' . $ejaculation->ejaculated_date->format('n月j日 H:i') . ')')
@endif

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-4">
            @component('components.profile', ['user' => $user])
            @endcomponent
        </div>
        <div class="col-lg-8">
            @if ($user->is_protected && !$user->isMe())
                <div class="card">
                    <div class="card-body">
                        <i class="ti ti-lock"></i> このユーザはチェックイン履歴を公開していません。
                    </div>
                </div>
            @elseif ($ejaculation->is_private && !$user->isMe())
                <div class="card">
                    <div class="card-body">
                        <i class="ti ti-lock"></i> 非公開チェックインのため、表示できません
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        @component('components.ejaculation', ['ejaculation' => $ejaculation, 'header' => 'span', 'likeUsersTall' => true, 'showSource' => true])
                        @endcomponent
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@component('components.delete-checkin-modal')
@endcomponent
@endsection
