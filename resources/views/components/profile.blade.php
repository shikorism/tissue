<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-row align-items-end">
            <img src="{{ $user->getProfileImageUrl(64) }}" class="rounded mr-2">
            <div class="d-flex flex-column overflow-hidden">
                <h4 class="card-title @if (Route::currentRouteName() === 'home') text-truncate @endif">
                    <a class="text-dark" href="{{ route('user.profile', ['name' => $user->name]) }}">{{ $user->display_name }}</a>
                </h4>
                <h6 class="card-subtitle">
                    <a class="text-muted" href="{{ route('user.profile', ['name' => $user->name]) }}">&commat;{{ $user->name }}</a>
                    @if ($user->is_protected)
                        <span class="oi oi-lock-locked text-muted"></span>
                    @endif
                </h6>
            </div>
        </div>

        {{-- Bio --}}
        @if (!empty($user->bio))
            <p class="card-text mt-3 mb-0">
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

@if (!$user->is_protected || $user->isMe())
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="font-weight-bold"><span class="oi oi-timer"></span> 現在のセッション</h6>
            @if (isset($currentSession))
                <p class="card-text mb-0">{{ $currentSession }}経過</p>
                <p class="card-text">({{ $latestEjaculation->ejaculated_date->format('Y/m/d H:i') }} にリセット)</p>
            @else
                <p class="card-text mb-0">計測がまだ始まっていません</p>
                <p class="card-text">(一度チェックインすると始まります)</p>
            @endif

            <h6 class="font-weight-bold"><span class="oi oi-graph"></span> 概況</h6>
            <p class="card-text mb-0">平均記録: {{ Formatter::formatInterval($summary[0]->average) }}</p>
            <p class="card-text mb-0">最長記録: {{ Formatter::formatInterval($summary[0]->longest) }}</p>
            <p class="card-text mb-0">最短記録: {{ Formatter::formatInterval($summary[0]->shortest) }}</p>
            <p class="card-text mb-0">合計時間: {{ Formatter::formatInterval($summary[0]->total_times) }}</p>
            <p class="card-text">通算回数: {{ $summary[0]->total_checkins }}回</p>
        </div>
    </div>
@endif
