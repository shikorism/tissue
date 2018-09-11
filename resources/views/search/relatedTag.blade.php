@extends('search.base')

@section('tab-content')
    <div class="row">
    @forelse($results as $tag)
        <p class="col-md-3"><a href="{{ route('search', ['q' => $tag->name]) }}" class="btn btn-outline-primary btn-block" role="button">{{ $tag->name }}</a></p>
    @empty
        <p class="col-12">このキーワードが含まれるタグはありません。</p>
    @endforelse
    </div>

    @if(!empty($results))
        <ul class="pagination mt-4 justify-content-center">
            <li class="page-item {{ $results->currentPage() === 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $results->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            @for ($i = 1; $i <= $results->lastPage(); $i++)
                <li class="page-item {{ $i === $results->currentPage() ? 'active' : '' }}"><a href="{{ $results->url($i) }}" class="page-link">{{ $i }}</a></li>
            @endfor
            <li class="page-item {{ $results->currentPage() === $results->lastPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $results->nextPageUrl() }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    @endif
@endsection