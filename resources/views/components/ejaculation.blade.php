<!-- span -->
<div>
    @switch ($header ?? 'user')
        @case ('user')
            <h5>
                <a href="{{ route('user.profile', ['name' => $ejaculation->user->name]) }}" class="text-dark"><img src="{{ $ejaculation->user->getProfileImageUrl(30) }}" srcset="{{ Formatter::profileImageSrcSet($ejaculation->user, 30) }}" width="30" height="30" class="rounded d-inline-block align-bottom"> <bdi>{{ $ejaculation->user->display_name }}</bdi></a>
                <a href="{{ route('checkin.show', ['id' => $ejaculation->id]) }}" class="text-muted"><small>{{ $ejaculation->ejaculated_date->format('Y/m/d H:i') }}</small></a>
            </h5>
            @break

        @case ('span')
            <h5>{{ $ejaculation->ejaculatedSpan() }} <a href="{{ route('checkin.show', ['id' => $ejaculation->id]) }}" class="text-muted"><small>{{ !empty($ejaculation->before_date) && !$ejaculation->discard_elapsed_time ? $ejaculation->before_date . ' ～ ' : '' }}{{ $ejaculation->ejaculated_date->format('Y/m/d H:i') }}</small></a></h5>
            @break
    @endswitch
</div>
<!-- tags -->
@if ($ejaculation->is_private || $ejaculation->source !== 'web' || $ejaculation->tags->isNotEmpty())
    <p class="tis-checkin-tags mb-2">
        @if ($ejaculation->is_private)
            <span class="badge badge-warning"><span class="oi oi-lock-locked"></span> 非公開</span>
        @endif
        @switch ($ejaculation->source)
            @case ('csv')
                <span class="badge badge-info"><span class="oi oi-cloud-upload"></span> インポート</span>
                @break
            @case ('webhook')
                <span class="badge badge-info" data-toggle="tooltip" title="Webhookからチェックイン"><span class="oi oi-flash"></span></span>
                @break
            @case ('api')
                <span class="badge badge-info" data-toggle="tooltip" title="APIからチェックイン"><span class="oi oi-flash"></span></span>
                @break
        @endswitch
        @foreach ($ejaculation->tags as $tag)
            <a class="badge badge-secondary" href="{{ route('search', ['q' => $tag->name]) }}"><span class="oi oi-tag"></span> {{ $tag->name }}</a>
        @endforeach
    </p>
@endif
<div class="{{ $ejaculation->isMuted() ? 'tis-checkin-muted' : '' }}">
    <!-- okazu link -->
    @if (!empty($ejaculation->link))
        <div class="row mx-0">
            @component('components.link-card', ['link' => $ejaculation->link, 'is_too_sensitive' => $ejaculation->is_too_sensitive])
            @endcomponent
            <p class="d-flex align-items-baseline mb-2 col-12 px-0">
                <span class="oi oi-link-intact mr-1"></span><a class="overflow-hidden" href="{{ $ejaculation->link }}" target="_blank" rel="noopener">{{ $ejaculation->link }}</a>
            </p>
        </div>
    @endif
    <!-- note -->
    @if (!empty($ejaculation->note))
        <p class="mb-2 text-break">
            {!! Formatter::linkify(nl2br(e($ejaculation->note))) !!}
        </p>
    @endif
</div>
@if ($ejaculation->isMuted())
    <div class="tis-checkin-muted-warning">
        このチェックインはミュートされています<br>クリックまたはタップで表示
    </div>
@endif
<!-- likes -->
@if ($ejaculation->likes_count > 0)
    <div class="my-2 py-1 border-top border-bottom d-flex align-items-center">
        <div class="ml-2 mr-3 text-secondary flex-shrink-0"><small><strong>{{ $ejaculation->likes_count }}</strong> 件のいいね</small></div>
        @if ($likeUsersTall ?? false)
            <div class="like-users-tall flex-grow-1 overflow-hidden">
                @foreach ($ejaculation->likes as $like)
                    @if ($like->user !== null)
                        <a href="{{ route('user.profile', ['name' => $like->user->name]) }}"><img src="{{ $like->user->getProfileImageUrl(36) }}" srcset="{{ Formatter::profileImageSrcSet($like->user, 36) }}" width="36" height="36" class="rounded" data-toggle="tooltip" data-placement="bottom" title="{{ $like->user->display_name }}"></a>
                    @endif
                @endforeach
            </div>
        @else
            <div class="like-users flex-grow-1 overflow-hidden">
                @foreach ($ejaculation->likes as $like)
                    @if ($like->user !== null)
                        <a href="{{ route('user.profile', ['name' => $like->user->name]) }}"><img src="{{ $like->user->getProfileImageUrl(30) }}" srcset="{{ Formatter::profileImageSrcSet($like->user, 30) }}" width="30" height="30" class="rounded" data-toggle="tooltip" data-placement="bottom" title="{{ $like->user->display_name }}"></a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endif
<!-- actions -->
<div class="ejaculation-actions">
    <button type="button" class="btn btn-link text-secondary"
            data-toggle="tooltip" data-placement="bottom"
            title="同じオカズでチェックイン" data-href="{{ $ejaculation->makeCheckinURL() }}"><span class="oi oi-reload"></span></button>
    <button type="button" class="btn btn-link text-secondary like-button"
            data-toggle="tooltip" data-placement="bottom" data-trigger="hover"
            title="いいね" data-id="{{ $ejaculation->id }}" data-liked="{{ (bool)$ejaculation->is_liked }}"><span class="oi oi-heart {{ $ejaculation->is_liked ? 'text-danger' : '' }}"></span><span class="like-count">{{ $ejaculation->likes_count ? $ejaculation->likes_count : '' }}</span></button>
    @auth
        @if (!empty($ejaculation->link))
            <span class="add-to-collection-button" data-link="{{ $ejaculation->link }}" data-tags="{{ $ejaculation->textTags() }}">
                <button type="button" class="btn btn-link text-secondary"
                        data-toggle="tooltip" data-placement="bottom" data-trigger="hover"
                        title="コレクションに追加"><span class="oi oi-plus"></span></button>
            </span>
        @endif
    @endauth
    @if ($ejaculation->user->isMe())
        <button type="button" class="btn btn-link text-secondary"
                data-toggle="tooltip" data-placement="bottom"
                title="修正" data-href="{{ route('checkin.edit', ['id' => $ejaculation->id]) }}"><span class="oi oi-pencil"></span></button>
        <button type="button" class="btn btn-link text-secondary"
                data-toggle="tooltip" data-placement="bottom"
                title="削除" data-target="#deleteCheckinModal" data-id="{{ $ejaculation->id }}" data-date="{{ $ejaculation->ejaculated_date }}"><span class="oi oi-trash"></span></button>
    @endif
</div>
