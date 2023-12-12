@extends('user.base')

@section('title', $user->display_name . ' (@' . $user->name . ') さんがいいねしたチェックイン')

@section('tab-content')
@if (($user->is_protected || $user->private_likes) && !$user->isMe())
    <p class="mt-4">
        <i class="ti ti-lock"></i> このユーザはいいね一覧を公開していません。
    </p>
@else
    <ul class="list-group">
        @forelse ($likes as $like)
            <li class="list-group-item border-bottom-only pt-3 pb-3 text-break">
                @component('components.ejaculation', ['ejaculation' => $like->ejaculation])
                @endcomponent
            </li>
        @empty
            <li class="list-group-item border-bottom-only">
                <p>まだ何もいいと思ったことがありません。</p>
            </li>
        @endforelse
    </ul>
    {{ $likes->links(null, ['className' => 'mt-4 justify-content-center']) }}
@endif

@component('components.delete-checkin-modal')
@endcomponent
@endsection
