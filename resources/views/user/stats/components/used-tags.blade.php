<div class="row">
    <div class="col-md-6">
        <h5 class="mb-4">最も使用したタグ</h5>
        @if ($tags->isEmpty())
            <div class="alert alert-secondary">
                データがありません
            </div>
        @else
            <table class="table table-striped border">
                <tbody>
                @foreach ($tags as $tag)
                    <tr>
                        <td><a class="text-reset" href="{{ route('search', ['q' => $tag->name]) }}"><span class="oi oi-tag text-secondary mr-2"></span>{{ $tag->name }}</a></td>
                        <td class="text-right">{{ number_format($tag->count) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div class="col-md-6">
        <h5 class="mb-4">オカズを含む最も使用したタグ</h5>
        @if ($tagsIncludesMetadata->isEmpty())
            <div class="alert alert-secondary">
                データがありません
            </div>
        @else
            <table class="table table-striped border">
                <tbody>
                @foreach ($tagsIncludesMetadata as $tag)
                    <tr>
                        <td><a class="text-reset" href="{{ route('search', ['q' => $tag->name]) }}"><span class="oi oi-tag text-secondary mr-2"></span>{{ $tag->name }}</a></td>
                        <td class="text-right">{{ number_format($tag->count) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
