@extends('layouts.base')

@section('head')
<style>
    #ejaculations .title {
        font-size: large;
    }
    #ejaculations .note {
        margin: 8px 0;
    }
    .no-border {
        border: none;
    }
    .pagination {
        padding-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <img src="{{ $user->getProfileImageUrl(64) }}" class="rounded mb-1">
                    <h4 class="card-title"><a class="text-dark" href="{{ route('profile', ['name' => $user->name]) }}">{{ $user->display_name }}</a></h4>
                    <h6 class="card-subtitle mb-4"><a class="text-muted" href="{{ route('profile', ['name' => $user->name]) }}">&commat;{{ $user->name }}</a></h6>

                    <h6 class="font-weight-bold"><span class="oi oi-timer"></span> 現在のセッション</h6>
                    @if (isset($currentSession))
                        <p class="card-text mb-0">{{ $currentSession }}経過</p>
                        <p class="card-text">({{ $ejaculations[0]['ejaculated_date'] }} にリセット)</p>
                    @else
                        <p class="card-text mb-0">計測がまだ始まっていません</p>
                        <p class="card-text">(一度チェックインすると始まります)</p>
                    @endif

                    <h6 class="font-weight-bold"><span class="oi oi-graph"></span> 概況</h6>
                    <p class="card-text mb-0">平均記録: {{ Formatter::formatInterval($summary[0]->average) }}</p>
                    <p class="card-text mb-0">最長記録: {{ Formatter::formatInterval($summary[0]->longest) }}</p>
                    <p class="card-text mb-0">最短記録: {{ Formatter::formatInterval($summary[0]->shortest) }}</p>
                    <p class="card-text mb-0">合計時間: {{ Formatter::formatInterval($summary[0]->total_times) }}</p>
                    <p class="card-text">通算回数: {{ $summary[0]->total_checkins }}回</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#timeline" data-toggle="tab" role="tab">タイムライン</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#stats" data-toggle="tab" role="tab">グラフ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#okazu" data-toggle="tab" role="tab">オカズ</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="timeline" role="tabpanel">
                    <ul class="list-group">
                        @forelse ($ejaculations as $ejaculation)
                            <li class="list-group-item border-bottom-only pt-3 pb-3">
                                <!-- span -->
                                <div class="d-flex justify-content-between">
                                    <h5>{{ $ejaculation['ejaculated_span'] ?? '精通' }} <small class="text-muted">{{ $ejaculation['before_date'] }}{{ !empty($ejaculation['before_date']) ? ' ～ ' : '' }}{{ $ejaculation['ejaculated_date'] }}</small></h5>
                                    <div>
                                        <a class="text-secondary timeline-action-item" href="#" data-toggle="tooltip" data-placement="bottom" title="修正"><span class="oi oi-pencil"></span></a>
                                        <a class="text-secondary timeline-action-item" href="#" data-toggle="tooltip" data-placement="bottom" title="削除"><span class="oi oi-trash"></span></a>
                                    </div>
                                </div>
                                <!-- tags -->
                                @if ($ejaculation['is_private']) {{-- TODO: タグを付けたら、タグが空じゃないかも判定に加える --}}
                                    <p class="mb-2">
                                        @if ($ejaculation['is_private'])
                                            <span class="badge badge-warning"><span class="oi oi-lock-locked"></span> 非公開</span>
                                        @endif
                                        {{--
                                        <span class="badge badge-secondary"><span class="oi oi-tag"></span> 催眠音声</span>
                                        <span class="badge badge-secondary"><span class="oi oi-tag"></span> 適当なタグ</span>
                                        --}}
                                    </p>
                                @endif
                                <!-- okazu link -->
                                {{--
                                <div class="card mb-2 w-50" style="font-size: small;">
                                    <a class="text-dark card-link" href="#">
                                        <img src="holder.js/320x240" alt="Thumbnail" class="card-img-top">
                                        <div class="card-body">
                                            <h6 class="card-title">タイトル</h6>
                                            <p class="card-text">コンテンツの説明文</p>
                                        </div>
                                    </a>
                                </div>
                                --}}
                                <!-- note -->
                                @if (!empty($ejaculation['note']))
                                    <p class="mb-0">
                                        {{ $ejaculation['note'] }}
                                    </p>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item border-bottom-only">
                                <p>まだチェックインしていません。</p>
                            </li>
                        @endforelse
                    </ul>
                    <ul class="pagination mt-4 justify-content-center">
                        <li class="page-item {{ $ejaculations->currentPage() === 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $ejaculations->previousPageUrl() }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        @for ($i = 1; $i <= $ejaculations->lastPage(); $i++)
                            <li class="page-item {{ $i === $ejaculations->currentPage() ? 'active' : '' }}"><a href="{{ $ejaculations->url($i) }}" class="page-link">{{ $i }}</a></li>
                        @endfor
                        <li class="page-item {{ $ejaculations->currentPage() === $ejaculations->lastPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $ejaculations->nextPageUrl() }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane" id="stats" role="tabpanel">
                    <div class="alert alert-light" role="alert">
                        ここには何のグラフを置くか決めていません。
                    </div>
                </div>
                <div class="tab-pane" id="okazu" role="tabpanel">
                    <div class="alert alert-light" role="alert">
                        ここには過去のチェックインに添付したオカズがリストアップされます。
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
