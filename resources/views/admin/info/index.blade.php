@extends('layouts.admin')

@section('title', 'お知らせ')

@section('tab-content')
    <div class="container">
        <h2>お知らせ</h2>
        <hr>
        <div class="d-flex mb-3">
            <a href="{{ route('admin.info.create') }}" class="btn btn-primary">新規作成</a>
        </div>
        <table class="table table-sm">
            <thead>
            <tr>
                <th>カテゴリ</th>
                <th>タイトル</th>
                <th>作成日</th>
            </tr>
            </thead>
            <tbody>
            @foreach($informations as $info)
                <tr>
                    <td>
                        @if ($info->pinned)
                            <span class="badge badge-secondary"><span class="oi oi-pin"></span>ピン留め</span>
                        @endif
                        <span class="badge {{ $categories[$info->category]['class'] }}">{{ $categories[$info->category]['label'] }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.info.edit', ['id' => $info->id]) }}">{{ $info->title }}</a>
                    </td>
                    <td>
                        {{ $info->created_at->format('Y年n月j日') }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <ul class="pagination mt-4 justify-content-center">
            <li class="page-item {{ $informations->currentPage() === 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $informations->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            @for ($i = 1; $i <= $informations->lastPage(); $i++)
                <li class="page-item {{ $i === $informations->currentPage() ? 'active' : '' }}"><a href="{{ $informations->url($i) }}" class="page-link">{{ $i }}</a></li>
            @endfor
            <li class="page-item {{ $informations->currentPage() === $informations->lastPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $informations->nextPageUrl() }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    </div>
@endsection