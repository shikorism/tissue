@extends('user.base')

@section('title', $collection->title . ' (@' . $user->name . ')')

@push('head')
@endpush

@section('sidebar')
    <div class="card mb-4">
        <div class="card-header">
            コレクション
        </div>
        <div class="list-group list-group-flush">
            @foreach ($collections as $col)
                @if ($col->id === $collection->id)
                    <li class="list-group-item d-flex justify-content-between align-items-center active">
                        <div style="word-break: break-all;">
                            <span class="oi oi-folder mr-1"></span>{{ $col->title }}
                        </div>
                    </li>
                @else
                    <a class="list-group-item d-flex justify-content-between align-items-center text-dark"
                       href="{{ route('user.collections.show', ['name' => $user->name, 'id' => $col->id]) }}">
                        <div style="word-break: break-all;">
                            <span class="oi oi-folder text-secondary mr-1"></span>{{ $col->title }}
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
@endsection

@section('tab-content')
    <ul class="list-group">
        @forelse ($items as $item)
            <li class="list-group-item border-bottom-only pt-3 pb-3 text-break">
                {{-- link --}}
                <div class="row mx-0">
                    @component('components.link-card', ['link' => $item->link, 'is_too_sensitive' => false])
                    @endcomponent
                    <p class="d-flex align-items-baseline mb-2 col-12 px-0">
                        <span class="oi oi-link-intact mr-1"></span><a class="overflow-hidden" href="{{ $item->link }}" target="_blank" rel="noopener">{{ $item->link }}</a>
                    </p>
                </div>
                {{-- tags --}}
                @if ($item->tags->isNotEmpty())
                    <p class="tis-checkin-tags mb-2">
                        @foreach ($item->tags as $tag)
                            <a class="badge badge-secondary" href="{{ route('search', ['q' => $tag->name]) }}"><span class="oi oi-tag"></span> {{ $tag->name }}</a>
                        @endforeach
                    </p>
                @endif
                {{-- note --}}
                @if (!empty($item->note))
                    <p class="mb-2 text-break">
                        {!! Formatter::linkify(nl2br(e($item->note))) !!}
                    </p>
                @endif
                {{-- actions --}}
                <div class="ejaculation-actions">
                    <button type="button" class="btn btn-link text-secondary"
                            data-toggle="tooltip" data-placement="bottom"
                            title="このオカズでチェックイン" data-href="{{ $item->makeCheckinURL() }}"><span class="oi oi-check"></span></button>
                    <span class="dropdown">
                        <button type="button" class="btn btn-link text-secondary"
                                data-toggle="dropdown" data-tooltip="tooltip" data-placement="bottom" data-trigger="hover"
                                title="コレクションに追加"><span class="oi oi-plus"></span></button>
                        <div class="dropdown-menu">
                            {{-- TODO: そのうち複数のコレクションを扱えるようにする --}}
                            <h6 class="dropdown-header">コレクションに追加</h6>
                            <button type="button" class="dropdown-item use-later-button" data-link="{{ $item->link }}">あとで抜く</button>
                        </div>
                    </span>
                    @if ($collection->user->isMe())
                        {{--<button type="button" class="btn btn-link text-secondary"
                                data-toggle="tooltip" data-placement="bottom"
                                title="修正" data-href=""><span class="oi oi-pencil"></span></button>--}}
                        <button type="button" class="btn btn-link text-secondary"
                                data-toggle="tooltip" data-placement="bottom"
                                title="削除" data-target="#deleteCollectionItemModal" data-collection-id="{{ $collection->id }}" data-item-id="{{ $item->id }}" data-link="{{ $item->link }}"><span class="oi oi-trash"></span></button>
                    @endif
                </div>
            </li>
        @empty
            <li class="list-group-item border-bottom-only">
                <p>このコレクションにはまだオカズが登録されていません。</p>
            </li>
        @endforelse
    </ul>
    {{ $items->links(null, ['className' => 'mt-4 justify-content-center']) }}

    @component('components.modal', ['id' => 'deleteCollectionItemModal'])
        @slot('title')
            削除確認
        @endslot
        <a class="link-label" target="_blank" rel="noopener"></a> をコレクションから削除してもよろしいですか？
        @slot('footer')
            <form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <button type="submit" class="btn btn-danger">削除</button>
            </form>
        @endslot
    @endcomponent
@endsection

@push('script')
@endpush

