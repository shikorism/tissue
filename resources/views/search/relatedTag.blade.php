@extends('search.base')

@section('tab-content')
    <div class="row">
    @forelse($results as $tag)
        <p class="col-md-3 text-break"><a href="{{ route('search', ['q' => $tag->name]) }}" class="btn btn-outline-primary btn-block text-truncate" role="button" title="{{ $tag->name }}">{{ $tag->name }}</a></p>
    @empty
        <p class="col-12">このキーワードが含まれるタグはありません。</p>
    @endforelse
    </div>
    {{ $results->links(null, ['className' => 'mt-4 justify-content-center']) }}
@endsection
