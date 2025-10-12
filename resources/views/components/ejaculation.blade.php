<!-- span -->
<div>
    @switch ($header ?? 'user')
        @case ('user')
            <h5>
                <a href="{{ url('/user/' . $ejaculation->user->name) }}" class="text-dark"><img src="{{ $ejaculation->user->getProfileImageUrl(30) }}" srcset="{{ Formatter::profileImageSrcSet($ejaculation->user, 30) }}" width="30" height="30" class="rounded d-inline-block align-bottom"> <bdi>{{ $ejaculation->user->display_name }}</bdi></a>
                <a href="{{ url('/checkin/' . $ejaculation->id) }}" class="text-muted"><small>{{ $ejaculation->ejaculated_date->format('Y/m/d H:i') }}</small></a>
            </h5>
            @break

        @case ('span')
            <h5>{{ $ejaculation->ejaculatedSpan() }} <a href="{{ url('/checkin/' . $ejaculation->id) }}" class="text-muted"><small>{{ !empty($ejaculation->before_date) && !$ejaculation->discard_elapsed_time ? $ejaculation->before_date . ' ～ ' : '' }}{{ $ejaculation->ejaculated_date->format('Y/m/d H:i') }}</small></a></h5>
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
            <a class="badge badge-secondary" href="{{ url()->query('/search', ['q' => $tag->name]) }}"><i class="ti ti-tag-filled"></i> {{ $tag->name }}</a>
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
