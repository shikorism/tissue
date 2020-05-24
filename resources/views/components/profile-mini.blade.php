<div class="d-flex flex-row align-items-end {{ $class ?? '' }}">
    <img src="{{ $user->getProfileImageUrl(48) }}" srcset="{{ Formatter::profileImageSrcSet($user, 48) }}" class="rounded mr-2">
    <div class="d-flex flex-column overflow-hidden">
        <h5 class="card-title text-truncate">
            <a class="text-dark" href="{{ route('user.profile', ['name' => $user->name]) }}">{{ $user->display_name }}</a>
        </h5>
        <h6 class="card-subtitle">
            <a class="text-muted" href="{{ route('user.profile', ['name' => $user->name]) }}">&commat;{{ $user->name }}</a>
            @if ($user->is_protected)
                <span class="oi oi-lock-locked text-muted"></span>
            @endif
        </h6>
    </div>
</div>
