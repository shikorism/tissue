@extends('search.base')

@section('tab-content')
    @if(empty($results))
        <p>このタグが含まれるチェックインはありません。</p>
    @else
        <ul class="list-group">
        @foreach($results as $ejaculation)
            <li class="list-group-item border-bottom-only pt-3 pb-3 tis-word-wrap">
                <!-- span -->
                <div class="d-flex justify-content-between">
                    <h5>
                        <a href="{{ route('user.profile', ['id' => $ejaculation->user->name]) }}" class="text-dark"><img src="{{ $ejaculation->user->getProfileImageUrl(30) }}" width="30" height="30" class="rounded d-inline-block align-bottom"> {{ $ejaculation->user->display_name }}</a>
                        <a href="{{ route('checkin.show', ['id' => $ejaculation->id]) }}" class="text-muted"><small>{{ $ejaculation->ejaculated_date->format('Y/m/d H:i') }}</small></a>
                    </h5>
                    <div>
                        <a class="text-secondary timeline-action-item" href="{{ route('checkin', ['link' => $ejaculation->link, 'tags' => $ejaculation->textTags()]) }}"><span class="oi oi-reload" data-toggle="tooltip" data-placement="bottom" title="同じオカズでチェックイン"></span></a>
                        @if ($ejaculation->user->isMe())
                            <a class="text-secondary timeline-action-item" href="{{ route('checkin.edit', ['id' => $ejaculation->id]) }}"><span class="oi oi-pencil" data-toggle="tooltip" data-placement="bottom" title="修正"></span></a>
                            <a class="text-secondary timeline-action-item" href="#" data-toggle="modal" data-target="#deleteCheckinModal" data-id="{{ $ejaculation->id }}" data-date="{{ $ejaculation->ejaculated_date }}"><span class="oi oi-trash" data-toggle="tooltip" data-placement="bottom" title="削除"></span></a>
                        @endif
                    </div>
                </div>
                <!-- tags -->
                @if ($ejaculation->is_private || $ejaculation->tags->isNotEmpty())
                    <p class="mb-2">
                        @if ($ejaculation->is_private)
                            <span class="badge badge-warning"><span class="oi oi-lock-locked"></span> 非公開</span>
                        @endif
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
                    <p class="mb-0 tis-word-wrap">
                        {!! Formatter::linkify(nl2br(e($ejaculation->note))) !!}
                    </p>
                @endif
            </li>
        @endforeach
        </ul>
        {{ $results->links(null, ['className' => 'mt-4 justify-content-center']) }}
    @endif
@endsection
