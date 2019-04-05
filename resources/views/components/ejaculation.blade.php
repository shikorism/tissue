<!-- span -->
<div class="d-flex justify-content-between flex-column flex-lg-row">
    <h5>
        <a href="{{ route('user.profile', ['id' => $ejaculation->user->name]) }}" class="text-dark"><img src="{{ $ejaculation->user->getProfileImageUrl(30) }}" width="30" height="30" class="rounded d-inline-block align-bottom"> <bdi>{{ $ejaculation->user->display_name }}</bdi></a>
        <a href="{{ route('checkin.show', ['id' => $ejaculation->id]) }}" class="text-muted"><small>{{ $ejaculation->ejaculated_date->format('Y/m/d H:i') }}</small></a>
    </h5>
    <div class="d-flex justify-content-between mb-2 mb-lg-0">
        <button type="button" class="btn btn-link text-secondary like-button" data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="いいね" data-id="{{ $ejaculation->id }}" data-liked="{{ (bool)$ejaculation->is_liked }}"><span class="oi oi-heart {{ $ejaculation->is_liked ? 'text-danger' : '' }}"></span><span class="like-count">{{ $ejaculation->likes_count ? $ejaculation->likes_count : '' }}</span></button>
        <button type="button" class="btn btn-link text-secondary" data-toggle="tooltip" data-placement="bottom" title="同じオカズでチェックイン" data-href="{{ route('checkin', ['link' => $ejaculation->link, 'tags' => $ejaculation->textTags()]) }}"><span class="oi oi-reload"></span></button>
    </div>
</div>
<!-- tags -->
@if ($ejaculation->tags->isNotEmpty())
    <p class="mb-2">
        @foreach ($ejaculation->tags as $tag)
            <a class="badge badge-secondary" href="{{ route('search', ['q' => $tag->name]) }}"><span class="oi oi-tag"></span> {{ $tag->name }}</a>
        @endforeach
    </p>
@endif
<!-- okazu link -->
@if (!empty($ejaculation->link))
    <div class="row mx-0">
        @component('components.link-card', ['link' => $ejaculation->link])
        @endcomponent
        <p class="d-flex align-items-baseline mb-2 col-12 px-0">
            <span class="oi oi-link-intact mr-1"></span><a class="overflow-hidden" href="{{ $ejaculation->link }}" target="_blank" rel="noopener">{{ $ejaculation->link }}</a>
        </p>
    </div>
@endif
<!-- note -->
@if (!empty($ejaculation->note))
    <p class="mb-0 text-break">
        {!! Formatter::linkify(nl2br(e($ejaculation->note))) !!}
    </p>
@endif