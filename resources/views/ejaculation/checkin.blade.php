@extends('layouts.base')

@section('title', 'チェックイン')

@section('content')
<div class="container">
    <h2>今致してる？</h2>
    <hr>
    <div class="row justify-content-center mt-5">
        <div class="col-lg-6">
            <form method="post" action="{{ route('checkin') }}">
                {{ csrf_field() }}

                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="date"><span class="oi oi-calendar"></span> 日付</label>
                        <input id="date" name="date" type="text" class="form-control {{ $errors->has('date') || $errors->has('datetime') ? ' is-invalid' : '' }}"
                               pattern="^20[0-9]{2}/(0[1-9]|1[0-2])/(0[1-9]|[12][0-9]|3[01])$" value="{{ old('date') ?? $defaults['date'] }}" required>

                        @if ($errors->has('date'))
                            <div class="invalid-feedback">{{ $errors->first('date') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="time"><span class="oi oi-clock"></span> 時刻</label>
                        <input id="time" name="time" type="text" class="form-control {{ $errors->has('time') || $errors->has('datetime') ? ' is-invalid' : '' }}"
                               pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$" value="{{ old('time') ?? $defaults['time'] }}" required>

                        @if ($errors->has('time'))
                            <div class="invalid-feedback">{{ $errors->first('time') }}</div>
                        @endif
                    </div>
                    @if ($errors->has('datetime'))
                        <div class="form-group col-sm-12" style="margin-top: -1rem;">
                            <small class="text-danger">{{ $errors->first('datetime') }}</small>
                        </div>
                    @endif
                </div>
                <div class="form-row">
                    <div class="form-group col-sm-12">
                        <input name="tags" type="hidden" value="{{ old('tags') ?? $defaults['tags'] }}">
                        <label for="tagInput"><span class="oi oi-tags"></span> タグ</label>
                        <div class="form-control {{ $errors->has('tags') ? ' is-invalid' : '' }}">
                            <ul id="tags" class="list-inline d-inline"></ul>
                            <input id="tagInput" type="text" style="outline: 0; border: 0;">
                        </div>
                        <small class="form-text text-muted">
                            Tab, Enter, 半角スペースのいずれかで入力確定します。
                        </small>

                        @if ($errors->has('tags'))
                            <div class="invalid-feedback">{{ $errors->first('tags') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm-12">
                        <label for="link"><span class="oi oi-link-intact"></span> オカズリンク</label>
                        <input id="link" name="link" type="text" autocomplete="off" class="form-control {{ $errors->has('link') ? ' is-invalid' : '' }}" placeholder="http://..." value="{{ old('link') ?? $defaults['link'] }}">
                        <small class="form-text text-muted">
                            オカズのURLを貼り付けて登録することができます。
                        </small>
                        @if ($errors->has('link'))
                            <div class="invalid-feedback">{{ $errors->first('link') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm-12">
                        <label for="note"><span class="oi oi-comment-square"></span> ノート</label>
                        <textarea id="note" name="note" class="form-control {{ $errors->has('note') ? ' is-invalid' : '' }}" rows="4">{{ old('note') ?? $defaults['note'] }}</textarea>
                        <small class="form-text text-muted">
                            最大 500 文字
                        </small>
                        @if ($errors->has('note'))
                            <div class="invalid-feedback">{{ $errors->first('note') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-row mt-4">
                    <p>オプション</p>
                    <div class="form-group col-sm-12">
                        <div class="custom-control custom-checkbox mb-3">
                            <input id="isPrivate" name="is_private" type="checkbox" class="custom-control-input" {{ old('is_private') || $defaults['is_private'] ? 'checked' : '' }}>
                            <label class="custom-control-label" for="isPrivate">
                            <span class="oi oi-lock-locked"></span> このチェックインを非公開にする
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button class="btn btn-primary" type="submit">チェックイン</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        function updateTags() {
            $('input[name=tags]').val(
                $('#tags')
                    .find('li')
                    .map(function () {
                        return $(this).data('value');
                    })
                    .get()
                    .join(' ')
            );
        }
        function insertTag(value) {
            $('#tags').append('<li class="list-inline-item badge badge-primary" style="cursor: pointer;" data-value="' + value + '"><span class="oi oi-tag"></span> ' + value + ' | x</li>');
        }
        var initTags = $('input[name=tags]').val();
        if (initTags.trim() !== '') {
            initTags.split(' ').forEach(function (value) {
                insertTag(value);
            });
        }

        $('#tagInput').on('keydown', function (ev) {
            var $this = $(this);
            if ($this.val().trim() !== '') {
                switch (ev.key) {
                    case 'Tab':
                    case 'Enter':
                    case ' ':
                        insertTag($this.val().trim());
                        $this.val('');
                        updateTags();
                        ev.preventDefault();
                        break;
                }
            } else if (ev.key === 'Enter') {
                // 誤爆防止
                ev.preventDefault();
            }
        });
        $('#tags')
            .on('click', 'li', function (ev) {
                $(this).remove();
                updateTags();
            })
            .parent()
            .on('click', function (ev) {
                $('#tagInput').focus();
            });
    </script>
@endpush