@extends('search.base')

@section('tab-content')
    @if(empty($results))
        <p>このタグが含まれるコレクションはありません。</p>
    @else
        <ul class="list-group">
        @foreach($results as $item)
            <li class="list-group-item border-bottom-only pt-3 pb-3 text-break">
                <h6>
                    <a href="{{ route('user.collections.show', ['name' => $item->collection->user->name, 'id' => $item->collection_id]) }}" class="text-dark"><i class="ti ti-folder text-secondary"></i> {{ $item->collection->title }}</a><span class="mx-1 text-muted">—</span><a href="{{ route('user.profile', ['name' => $item->collection->user->name]) }}" class="text-dark"><img src="{{ $item->collection->user->getProfileImageUrl(20) }}" srcset="{{ Formatter::profileImageSrcSet($item->collection->user, 20) }}" width="20" height="20" class="rounded d-inline-block align-bottom"> <bdi>{{ $item->collection->user->display_name }}</bdi> さんのコレクション</a>
                </h6>
                <div class="row mx-0">
                    @component('components.link-card', ['link' => $item->link, 'is_too_sensitive' => false])
                    @endcomponent
                    <p class="d-flex align-items-baseline mb-2 col-12 px-0">
                        <i class="ti ti-link mr-1"></i><a class="overflow-hidden" href="{{ $item->link }}" target="_blank" rel="noopener">{{ $item->link }}</a>
                    </p>
                </div>
                @if ($item->tags->isNotEmpty())
                    <p class="tis-checkin-tags mb-2">
                        @foreach ($item->tags as $tag)
                            <a class="badge badge-secondary" href="{{ route('search.collection', ['q' => $tag->name]) }}"><i class="ti ti-tag-filled"></i> {{ $tag->name }}</a>
                        @endforeach
                    </p>
                @endif
                @if (!empty($item->note))
                    <p class="mb-2 text-break">
                        {!! Formatter::linkify(nl2br(e($item->note))) !!}
                    </p>
                @endif
                <div class="ejaculation-actions">
                    <button type="button" class="btn text-secondary"
                            data-toggle="tooltip" data-placement="bottom"
                            title="このオカズでチェックイン" data-href="{{ $item->makeCheckinURL() }}"><i class="ti ti-send"></i></button>
                    @auth
                        <span class="add-to-collection-button" data-link="{{ $item->link }}" data-tags="{{ $item->textTags() }}">
                            <button type="button" class="btn text-secondary"
                                    data-toggle="tooltip" data-placement="bottom" data-trigger="hover"
                                    title="コレクションに追加"><i class="ti ti-folder-plus"></i></button>
                        </span>
                    @endauth
                </div>
            </li>
        @endforeach
        </ul>
        {{ $results->links(null, ['className' => 'mt-4 justify-content-center']) }}
    @endif
@endsection
