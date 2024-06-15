<h5 class="mb-4">最も使用したタグ</h5>
<div class="row">
    <div class="col-md-6">
        <h6 class="mb-2 text-center"><span class="tis-stat-table-category-checkin">チェックインタグ</span></h6>
        <p class="mb-3 text-center"><small class="text-muted">チェックインに追加したタグの集計です。</small></p>
        @if ($tags->isEmpty())
            <div class="alert alert-secondary">
                データがありません
            </div>
        @else
            <table class="table table-striped border tis-stat-table">
                <tbody>
                @foreach ($tags as $tag)
                    <tr>
                        <td style="word-break: break-all;">
                            <a class="text-reset" href="{{ route('search', ['q' => $tag->name]) }}"><i class="ti ti-tag text-secondary mr-2 d-inline-block"></i>{{ $tag->name }}</a>
                        </td>
                        <td class="text-right">
                            <a class="text-reset text-decoration-none" href="{{ route('search', ['q' => $tag->name]) }}">{{ number_format($tag->count) }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div class="col-md-6">
        <h6 class="mt-3 mt-md-0 mb-2 text-center"><span class="tis-stat-table-category-checkin">チェックインタグ</span> + <span class="tis-stat-table-category-metadata">オカズタグ</span></h6>
        <p class="mb-3 text-center"><small class="text-muted">オカズ自体のタグも含めた集計です。</small></p>
        @if ($tagsIncludesMetadata->isEmpty())
            <div class="alert alert-secondary">
                データがありません
            </div>
        @else
            <table class="table table-striped border tis-stat-table">
                <tbody>
                @foreach ($tagsIncludesMetadata as $tag)
                    <tr>
                        <td style="word-break: break-all;">
                            <a class="text-reset" href="{{ route('search', ['q' => $tag->name]) }}"><i class="ti ti-tag text-secondary mr-2 d-inline-block"></i>{{ $tag->name }}</a>
                        </td>
                        <td class="text-right">
                            <a class="text-reset text-decoration-none" href="{{ route('search', ['q' => $tag->name]) }}">{{ number_format($tag->count) }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
