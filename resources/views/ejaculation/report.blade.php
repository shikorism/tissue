@extends('layouts.base')

@section('title', 'チェックインの問題を報告')

@section('content')
    <div class="container">
        <h2>チェックインの問題を報告</h2>
        <hr>
        <div class="row">
            <div class="offset-lg-3 col-lg-6">
                <form action="{{ route('checkin.report.store', ['ejaculation' => $ejaculation]) }}" method="post">
                    @csrf

                    <div class="form-group">
                        <label for="violated_rule">報告の理由</label>
                        <select id="violated_rule" name="violated_rule" class="form-control {{ $errors->has('violated_rule') ? ' is-invalid' : '' }}">
                            <option value="">(理由を選択してください)</option>
                            @foreach($rules as $rule)
                                <option value="{{ $rule->id }}" {{ old('violated_rule') == $rule->id ? 'selected' : '' }}>{{ $rule->summary }}</option>
                            @endforeach
                            <option value="other" {{ old('violated_rule') == 'other' ? 'selected' : '' }}>その他</option>
                        </select>

                        @if ($errors->has('violated_rule'))
                            <div class="invalid-feedback">{{ $errors->first('violated_rule') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="comment">詳しい内容</label>
                        <textarea class="form-control {{ $errors->has('comment') ? ' is-invalid' : '' }}" id="comment" name="comment" rows="3" maxlength="1000">{{ old('comment') }}</textarea>
                        <small class="form-text text-muted">
                            理由を補足する情報があれば記載してください。理由に「その他」を選んだ場合は必須です。<br>最大 1,000 文字
                        </small>

                        @if ($errors->has('comment'))
                            <div class="invalid-feedback">{{ $errors->first('comment') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <p>対象のチェックイン</p>
                        <div class="card">
                            <div class="card-body">
                                @component('components.ejaculation', ['ejaculation' => $ejaculation, 'showActions' => false])
                                @endcomponent
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4">
                        <strong>注意</strong><br>
                        正当な理由のない報告を繰り返した場合、サービス運営の妨害行為として報告者にペナルティが課される可能性があります。
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-primary" type="submit">報告</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
