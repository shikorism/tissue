@extends('layouts.admin')

@section('title', '通報理由')

@section('tab-content')
    <div class="container">
        <h2>通報理由の作成</h2>
        <hr>
        <form action="{{ route('admin.rule.store') }}" method="post">
            @csrf

            <div class="form-group">
                <label for="summary">ルール</label>
                <textarea class="form-control {{ $errors->has('summary') ? ' is-invalid' : '' }}" id="summary" name="summary" rows="3" maxlength="255">{{ old('summary') }}</textarea>
                <small class="form-text text-muted">
                    最大 255 文字
                </small>

                @if ($errors->has('summary'))
                    <div class="invalid-feedback">{{ $errors->first('summary') }}</div>
                @endif
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">登録</button>
            </div>
        </form>
    </div>
@endsection
