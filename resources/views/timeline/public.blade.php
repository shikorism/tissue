@extends('layouts.base')

@section('title', 'お惣菜コーナー')

@section('content')
    <div class="container pb-1">
        <h2 class="mb-3">お惣菜コーナー</h2>
        <p class="text-secondary">公開チェックインから、オカズリンク付きのものを表示しています。</p>
    </div>
    <div class="container-fluid">
        <div class="row mx-1">
            @foreach($ejaculations as $ejaculation)
                <div class="col-12 col-lg-6 col-xl-4 py-3 text-break border-top">
                    <!-- span -->
                    <div class="d-flex justify-content-between">
                        <h5>
                            <a href="{{ route('user.profile', ['id' => $ejaculation->user->name]) }}" class="text-dark"><img src="{{ $ejaculation->user->getProfileImageUrl(30) }}" width="30" height="30" class="rounded d-inline-block align-bottom"> <bdi>{{ $ejaculation->user->display_name }}</bdi></a>
                            <a href="{{ route('checkin.show', ['id' => $ejaculation->id]) }}" class="text-muted"><small>{{ $ejaculation->ejaculated_date->format('Y/m/d H:i') }}</small></a>
                        </h5>
                        <div>
                            <a class="text-secondary timeline-action-item" href="{{ route('checkin', ['link' => $ejaculation->link, 'tags' => $ejaculation->textTags()]) }}"><span class="oi oi-reload" data-toggle="tooltip" data-placement="bottom" title="同じオカズでチェックイン"></span></a>
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
                        <p class="mb-0 text-break">
                            {!! Formatter::linkify(nl2br(e($ejaculation->note))) !!}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
        {{ $ejaculations->links(null, ['className' => 'mt-4 justify-content-center']) }}
    </div>
@endsection
