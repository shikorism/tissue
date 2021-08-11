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
                        <span class="oi oi-lock-locked"></span> このユーザはチェックイン履歴を公開していません。
                    </div>
                </div>
            @elseif ($ejaculation->is_private && !$user->isMe())
                <div class="card">
                    <div class="card-body">
                        <span class="oi oi-lock-locked"></span> 非公開チェックインのため、表示できません
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        @component('components.ejaculation', ['ejaculation' => $ejaculation, 'span' => 'show', 'likeUsersTall' => true])
                        @endcomponent
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@component('components.modal', ['id' => 'deleteCheckinModal'])
    @slot('title')
        削除確認
    @endslot
    <span class="date-label"></span> のチェックインを削除してもよろしいですか？
    @slot('footer')
        <form action="{{ route('checkin.destroy', ['id' => '@']) }}" method="post">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
            <button type="submit" class="btn btn-danger">削除</button>
        </form>
    @endslot
@endcomponent
@endsection
