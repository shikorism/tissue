@extends('layouts.admin')

@section('title', 'お知らせ')

@section('tab-content')
    <div class="container">
        <h2>お知らせの編集</h2>
        <hr>
        <form action="{{ route('admin.info.update', ['id' => $info->id]) }}" method="post">
            {{ method_field('PUT') }}
            {{ csrf_field() }}

            <div class="row">
                <div class="form-group col-12 col-lg-6">
                    <label for="category">カテゴリ</label>
                    <select id="category" name="category" class="form-control">
                        @foreach($categories as $id => $category)
                            <option value="{{ $id }}" {{ (old('category') ?? $info->category) == $id ? 'checked' : '' }}>{{ $category['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6 d-flex flex-column justify-content-center">
                    <div class="custom-control custom-checkbox">
                        <input id="pinned" type="checkbox" class="custom-control-input"
                                {{ (is_bool(old('pinned')) ? old('pinned') : $info->pinned) ? 'checked' : ''}}>
                        <label for="pinned" class="custom-control-label">ピン留め (常に優先表示) する</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="title">タイトル</label>
                <input id="title" name="title" type="text" class="form-control" value="{{ old('title') ?? $info->title }}">
            </div>
            <div class="form-group mt-3">
                <label for="content">本文</label>
                <textarea id="content" name="content" rows="15" class="form-control" maxlength="10000">{{ old('content') ?? $info->content }}</textarea>
                <small class="form-text text-muted">
                    最大 10000 文字、Markdown 形式
                </small>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">更新</button>
                <button type="button" class="btn btn-danger">削除</button>
            </div>
        </form>
    </div>
@endsection