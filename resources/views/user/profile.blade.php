@extends('user.base')

@section('title', $user->display_name . ' (@' . $user->name . ')')

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
                        <div style="word-break: break-all;">
                            <i class="ti ti-tag text-secondary mr-2 d-inline-block"></i>{{ $tag->name }}
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
        <i class="ti ti-lock"></i> このユーザはチェックイン履歴を公開していません。
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
                @component('components.ejaculation', ['ejaculation' => $ejaculation, 'header' => 'span'])
                @endcomponent
            </li>
        @empty
            <li class="list-group-item border-bottom-only">
                <p>まだチェックインしていません。</p>
            </li>
        @endforelse
    </ul>
    {{ $ejaculations->links(null, ['className' => 'mt-4 justify-content-center']) }}
@endif

@component('components.delete-checkin-modal')
@endcomponent
@endsection

@push('script')
    @if (Route::currentRouteName() === 'user.profile')
        <script id="count-by-day" type="application/json">@json($countByDay)</script>
        @vite('resources/assets/js/user/profile.ts')
    @endif
@endpush

