@extends('search.base')

@section('tab-content')
    @if(empty($results))
        <p>このタグが含まれるチェックインはありません。</p>
    @else
        <ul class="list-group">
        @foreach($results as $ejaculation)
            <li class="list-group-item border-bottom-only pt-3 pb-3 text-break">
                @component('components.ejaculation', compact('ejaculation'))
                @endcomponent
            </li>
        @endforeach
        </ul>
        {{ $results->links(null, ['className' => 'mt-4 justify-content-center']) }}
    @endif

    @component('components.delete-checkin-modal')
    @endcomponent
@endsection
