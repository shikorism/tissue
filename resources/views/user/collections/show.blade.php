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
                        <span class="badge badge-light badge-pill">{{ $col->items()->count() }}</span>
                    </li>
                @else
                    <a class="list-group-item d-flex justify-content-between align-items-center text-dark"
                       href="{{ route('user.collections.show', ['name' => $user->name, 'id' => $col->id]) }}">
                        <div style="word-break: break-all;">
                            <span class="oi oi-folder text-secondary mr-1"></span>{{ $col->title }}
                        </div>
                        <span class="badge badge-secondary badge-pill">{{ $col->items()->count() }}</span>
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
                <a href="{{ $item->link }}" target="_blank" rel="noopener">{{ $item->link }}</a>
            </li>
        @empty
            <li class="list-group-item border-bottom-only">
                <p>このコレクションにはまだオカズが登録されていません。</p>
            </li>
        @endforelse
    </ul>
    {{ $items->links(null, ['className' => 'mt-4 justify-content-center']) }}
@endsection

@push('script')
@endpush

