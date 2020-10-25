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
                    <span class="oi oi-link-intact mr-1 mt-1"></span>
                    <a href="{{ $user->url }}" rel="me nofollow noopener" target="_blank" class="text-truncate">{{ preg_replace('~\Ahttps?://~', '', $user->url) }}</a>
                </p>
            @endif
        </div>
    </div>
@endif

@if (!$user->is_protected || $user->isMe())
    <div class="card mb-4">
        <div class="card-body">
            @component('components.profile-stats', ['user' => $user])
            @endcomponent
        </div>
    </div>
@endif
