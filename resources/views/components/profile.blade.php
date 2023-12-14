<div class="card mb-4">
    <div class="card-body">
        <img src="{{ $user->getProfileImageUrl(128) }}" srcset="{{ Formatter::profileImageSrcSet($user, 128) }}" class="rounded mb-1">
        <h4 class="card-title">
            <a class="text-dark" href="{{ route('user.profile', ['name' => $user->name]) }}">{{ $user->display_name }}</a>
        </h4>
        <h6 class="card-subtitle">
            <a class="text-muted" href="{{ route('user.profile', ['name' => $user->name]) }}">&commat;{{ $user->name }}</a>
            @if ($user->is_protected)
                <i class="ti ti-lock text-muted"></i>
            @endif
        </h6>

        {{-- Bio --}}
        @if (!empty($user->bio))
            <p class="card-text mt-3 mb-0">
                {!! Formatter::linkify(nl2br(e($user->bio))) !!}
            </p>
        @endif

        {{-- URL --}}
        @if (!empty($user->url))
            <p class="card-text d-flex mt-3">
                <i class="ti ti-link mr-1 mt-1"></i>
                <a href="{{ $user->url }}" rel="me nofollow noopener" target="_blank" class="text-truncate">{{ preg_replace('~\Ahttps?://~', '', $user->url) }}</a>
            </p>
        @endif
    </div>
</div>

@if (!$user->is_protected || $user->isMe())
    <div class="card mb-4">
        <div class="card-body">
            @component('components.profile-stats', ['user' => $user])
            @endcomponent
        </div>
    </div>
@endif
