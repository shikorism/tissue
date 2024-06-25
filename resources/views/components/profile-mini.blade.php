<div class="d-flex flex-row align-items-end {{ $class ?? '' }}">
    <img src="{{ $user->getProfileImageUrl(48) }}" srcset="{{ Formatter::profileImageSrcSet($user, 48) }}" class="rounded mr-2">
    <div class="d-flex flex-column overflow-hidden">
        <div class="tis-profile-mini-display-name text-truncate">
            <a class="text-dark" href="{{ route('user.profile', ['name' => $user->name]) }}">{{ $user->display_name }}</a>
        </div>
        <div class="tis-profile-mini-name">
            <a class="text-muted" href="{{ route('user.profile', ['name' => $user->name]) }}">&commat;{{ $user->name }}</a>
            @if ($user->is_protected)
                <i class="ti ti-lock text-muted"></i>
            @endif
        </div>
    </div>
</div>
