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
@if ($ejaculation->is_private || $ejaculation->source === 'csv' || $ejaculation->tags->isNotEmpty())
    <p class="tis-checkin-tags mb-2">
        @if ($ejaculation->is_private)
            <span class="badge badge-warning"><i class="ti ti-lock"></i> 非公開</span>
        @endif
        @if ($ejaculation->source === 'csv')
            <span class="badge badge-info"><i class="ti ti-cloud-upload"></i> インポート</span>
        @endif
        @foreach ($ejaculation->tags as $tag)
            <a class="badge badge-secondary" href="{{ route('search', ['q' => $tag->name]) }}"><i class="ti ti-tag-filled"></i> {{ $tag->name }}</a>
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
                <i class="ti ti-link mr-1"></i><a class="overflow-hidden" href="{{ $ejaculation->link }}" target="_blank" rel="noopener">{{ $ejaculation->link }}</a>
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
@if ($showSource ?? false)
    @switch ($ejaculation->source)
        @case ('webhook')
            <p class="mb-2 text-secondary small">Webhookからチェックイン</p>
            @break
        @case ('api')
            <p class="mb-2 text-secondary small">APIからチェックイン</p>
            @break
    @endswitch
@endif
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
@if ($showActions ?? true)
<div class="ejaculation-actions">
    <button type="button" class="btn text-secondary"
            data-toggle="tooltip" data-placement="bottom"
            title="同じオカズでチェックイン" data-href="{{ $ejaculation->makeCheckinURL() }}"><i class="ti ti-reload"></i></button>
    <button type="button" class="btn text-secondary like-button"
            data-toggle="tooltip" data-placement="bottom" data-trigger="hover"
            title="いいね" data-id="{{ $ejaculation->id }}" data-liked="{{ (bool)$ejaculation->is_liked }}"><i class="ti ti-heart-filled {{ $ejaculation->is_liked ? 'text-danger' : '' }}"></i><span class="like-count">{{ $ejaculation->likes_count ? $ejaculation->likes_count : '' }}</span></button>
    @auth
        @if (!empty($ejaculation->link))
            <span class="add-to-collection-button" data-link="{{ $ejaculation->link }}" data-tags="{{ $ejaculation->textTags() }}">
                <button type="button" class="btn text-secondary"
                        data-toggle="tooltip" data-placement="bottom" data-trigger="hover"
                        title="コレクションに追加"><i class="ti ti-folder-plus"></i></button>
            </span>
        @endif
    @endauth
    @if ($ejaculation->user->isMe())
        <button type="button" class="btn text-secondary"
                data-toggle="tooltip" data-placement="bottom"
                title="修正" data-href="{{ route('checkin.edit', ['id' => $ejaculation->id]) }}"><i class="ti ti-edit"></i></button>
        <button type="button" class="btn text-secondary"
                data-toggle="tooltip" data-placement="bottom"
                title="削除" data-target="#deleteCheckinModal" data-id="{{ $ejaculation->id }}" data-date="{{ $ejaculation->ejaculated_date }}"><i class="ti ti-trash"></i></button>
    @else
        <button type="button" class="btn text-secondary"
                data-toggle="tooltip" data-placement="bottom"
                title="問題を報告" data-href="{{ route('checkin.report', ['ejaculation' => $ejaculation]) }}"><i class="ti ti-flag"></i></button>
    @endif
</div>
@endif
