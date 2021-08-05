@extends('user.base')

@section('title', $user->display_name . ' (@' . $user->name . ')')

@push('head')
    @if (Route::currentRouteName() === 'user.profile')
        <link rel="stylesheet" href="//cdn.jsdelivr.net/cal-heatmap/3.3.10/cal-heatmap.css" />
    @endif
@endpush

@section('sidebar')
    {{-- TODO: タイムラインとオカズのテンプレを分けたら条件外す --}}
    @if (Route::currentRouteName() === 'user.profile')
    @component('components.profile-bio', ['user' => $user])
    @endcomponent
    @if (!$user->is_protected || $user->isMe())
        <div class="card mb-4">
            <div class="card-body">
                @component('components.profile-stats', ['user' => $user])
                @endcomponent
            </div>
        </div>
    @endif
    @if (!empty($tags) && (!$user->is_protected || $user->isMe()))
        <div class="card mb-4">
            <div class="card-header">
                よく使っているタグ
            </div>
            <div class="list-group list-group-flush">
                @foreach ($tags as $tag)
                    <a class="list-group-item d-flex justify-content-between align-items-center text-dark" href="{{ route('search', ['q' => $tag->name]) }}">
                        <div>
                            <span class="oi oi-tag text-secondary"></span>
                            {{ $tag->name }}
                        </div>
                        <span class="badge badge-secondary badge-pill">{{ $tag->count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
    @endif
@endsection

@section('tab-content')
@if ($user->is_protected && !$user->isMe())
    <p class="mt-4">
        <span class="oi oi-lock-locked"></span> このユーザはチェックイン履歴を公開していません。
    </p>
@else
    @if (Route::currentRouteName() === 'user.profile' && $ejaculations->count() !== 0 && $ejaculations->currentPage() === 1)
        <h5 class="mx-4 my-3">Shikontributions</h5>
        <div id="cal-heatmap" class="tis-contribution-graph mx-4 mt-3"></div>
        <hr class="mt-4 mb-2">
    @endif
    <ul class="list-group">
        @forelse ($ejaculations as $ejaculation)
            <li class="list-group-item border-bottom-only pt-3 pb-3 text-break">
                <!-- span -->
                <div>
                    <h5>{{ $ejaculation->ejaculatedSpan() }} <a href="{{ route('checkin.show', ['id' => $ejaculation->id]) }}" class="text-muted"><small>{{ !empty($ejaculation->before_date) && !$ejaculation->discard_elapsed_time ? $ejaculation->before_date . ' ～ ' : '' }}{{ $ejaculation->ejaculated_date->format('Y/m/d H:i') }}</small></a></h5>
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
                        <div class="like-users flex-grow-1 overflow-hidden">
                            @foreach ($ejaculation->likes as $like)
                                @if ($like->user !== null)
                                    <a href="{{ route('user.profile', ['name' => $like->user->name]) }}"><img src="{{ $like->user->getProfileImageUrl(30) }}" srcset="{{ Formatter::profileImageSrcSet($like->user, 30) }}" width="30" height="30" class="rounded" data-toggle="tooltip" data-placement="bottom" title="{{ $like->user->display_name }}"></a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
                <!-- actions -->
                <div class="ejaculation-actions">
                    <button type="button" class="btn btn-link text-secondary" data-toggle="tooltip" data-placement="bottom" title="同じオカズでチェックイン" data-href="{{ $ejaculation->makeCheckinURL() }}"><span class="oi oi-reload"></span></button>
                    <button type="button" class="btn btn-link text-secondary like-button" data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="いいね" data-id="{{ $ejaculation->id }}" data-liked="{{ (bool)$ejaculation->is_liked }}"><span class="oi oi-heart {{ $ejaculation->is_liked ? 'text-danger' : '' }}"></span><span class="like-count">{{ $ejaculation->likes_count ? $ejaculation->likes_count : '' }}</span></button>
                    @if ($user->isMe())
                        <button type="button" class="btn btn-link text-secondary" data-toggle="tooltip" data-placement="bottom" title="修正" data-href="{{ route('checkin.edit', ['id' => $ejaculation->id]) }}"><span class="oi oi-pencil"></span></button>
                        <button type="button" class="btn btn-link text-secondary" data-toggle="tooltip" data-placement="bottom" title="削除" data-target="#deleteCheckinModal" data-id="{{ $ejaculation->id }}" data-date="{{ $ejaculation->ejaculated_date }}"><span class="oi oi-trash"></span></button>
                    @endif
                </div>
            </li>
        @empty
            <li class="list-group-item border-bottom-only">
                <p>まだチェックインしていません。</p>
            </li>
        @endforelse
    </ul>
    {{ $ejaculations->links(null, ['className' => 'mt-4 justify-content-center']) }}
@endif

@component('components.modal', ['id' => 'deleteCheckinModal'])
    @slot('title')
        削除確認
    @endslot
    <span class="date-label"></span> のチェックインを削除してもよろしいですか？
    @slot('footer')
        <form action="{{ route('checkin.destroy', ['id' => '@']) }}" method="post">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
            <button type="submit" class="btn btn-danger">削除</button>
        </form>
    @endslot
@endcomponent
@endsection

@push('script')
    @if (Route::currentRouteName() === 'user.profile')
        <script id="count-by-day" type="application/json">@json($countByDay)</script>
        <script src="{{ mix('js/vendor/chart.js') }}"></script>
        <script src="{{ mix('js/user/profile.js') }}"></script>
    @endif
@endpush

