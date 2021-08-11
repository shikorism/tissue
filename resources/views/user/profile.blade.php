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
                @component('components.ejaculation', ['ejaculation' => $ejaculation, 'span' => 'withLink'])
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

