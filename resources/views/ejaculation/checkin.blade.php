@extends('layouts.base')

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
                               pattern="^20[0-9]{2}/(0[1-9]|1[0-2])/(0[1-9]|[12][0-9]|3[01])$" value="{{ old('date') ?? date('Y/m/d') }}" required>

                        @if ($errors->has('date'))
                            <div class="invalid-feedback">{{ $errors->first('date') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="time"><span class="oi oi-clock"></span> 時刻</label>
                        <input id="time" name="time" type="text" class="form-control {{ $errors->has('time') || $errors->has('datetime') ? ' is-invalid' : '' }}"
                               pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$" value="{{ old('time') ?? date('H:i') }}" required>

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
                {{--
                <div class="form-row">
                    <div class="form-group col-sm-12">
                        <label for="tags"><span class="oi oi-tags"></span> タグ</label>
                        <input id="tags" type="text" class="form-control" placeholder="未実装です" disabled>
                        <small class="form-text text-muted">
                            スペース区切りで複数入力できます。
                        </small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm-12">
                        <label for="link"><span class="oi oi-link-intact"></span> オカズリンク</label>
                        <input id="link" type="text" class="form-control" placeholder="https://...">
                        <small class="form-text text-muted">
                            オカズのURLを貼り付けて登録することができます。
                        </small>
                    </div>
                    <div class="form-group col-sm-12 d-none">
                        <div class="card">
                            <div class="card-header">このオカズで合っていますか？</div>
                            <div class="card-body">
                                <div class="d-flex mb-4">
                                    <img src="holder.js/128x128" alt="" class="rounded">
                                    <div class="align-self-center ml-2 mr-auto">
                                        <p class="mb-1">タイトル</p>
                                        <small class="text-muted">概要</small>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-success"><span class="oi oi-check"></span> 決定</a>
                                <a href="#" class="btn btn-outline-secondary">キャンセル</a>
                            </div>
                        </div>
                    </div>
                </div>
                --}}
                <div class="form-row">
                    <div class="form-group col-sm-12">
                        <label for="note"><span class="oi oi-comment-square"></span> ノート</label>
                        <textarea id="note" name="note" class="form-control {{ $errors->has('note') ? ' is-invalid' : '' }}" rows="4">{{ old('note') }}</textarea>
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
                        <div class="form-check">
                            <label class="custom-control custom-checkbox">
                                <input name="is_private" type="checkbox" class="custom-control-input" {{ old('is_private') ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">
                                <span class="oi oi-lock-locked"></span> このチェックインを非公開にする
                            </span>
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
@endpush