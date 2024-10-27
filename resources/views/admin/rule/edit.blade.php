@extends('layouts.admin')

@section('title', '通報理由')

@section('tab-content')
    <div class="container">
        <h2>通報理由の編集</h2>
        <hr>
        <form action="{{ route('admin.rule.update', ['rule' => $rule]) }}" method="post">
            @method('PUT')
            @csrf

            <div class="form-group">
                <label for="summary">ルール</label>
                <textarea class="form-control {{ $errors->has('summary') ? ' is-invalid' : '' }}" id="summary" name="summary" rows="3" maxlength="255">{{ old('summary') ?? $rule->summary }}</textarea>
                <small class="form-text text-muted">
                    最大 255 文字
                </small>

                @if ($errors->has('summary'))
                    <div class="invalid-feedback">{{ $errors->first('summary') }}</div>
                @endif
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">更新</button>
                <button type="submit" class="btn btn-danger" form="delete-form">削除</button>
            </div>
        </form>
        <form id="delete-form" action="{{ route('admin.rule.destroy', ['rule' => $rule]) }}" method="post">
            @method('DELETE')
            @csrf
        </form>
    </div>
@endsection
