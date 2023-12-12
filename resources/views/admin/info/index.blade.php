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
                            <span class="badge badge-secondary"><i class="ti ti-pinned-filled"></i>ピン留め</span>
                        @endif
                        <span class="badge {{ $categories[$info->category]['class'] }}">{{ $categories[$info->category]['label'] }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.info.edit', ['info' => $info->id]) }}">{{ $info->title }}</a>
                    </td>
                    <td>
                        {{ $info->created_at->format('Y年n月j日') }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $informations->links(null, ['className' => 'mt-4 justify-content-center']) }}
    </div>
@endsection
