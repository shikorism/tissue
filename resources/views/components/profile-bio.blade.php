@if (!empty($user->bio) || !empty($user->url))
    <div class="card mb-4">
        <div class="card-body">
            {{-- Bio --}}
            @if (!empty($user->bio))
                <p class="card-text mb-0">
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
@endif
