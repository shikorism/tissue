@extends('layouts.base-old')

@section('content')
<div class="container">
    <h2 class="grey-text">今致してる？</h2>
    <div class="row">
        <form method="post" action="{{ route('checkin') }}" class="col s12 push-m3 m6">
            {{ csrf_field() }}

            <div class="card-panel">
                <div class="row">
                    <div class="input-field col s6">
                        <i class="material-icons prefix">today</i>
                        <input id="date" name="date" class="validate{{ $errors->has('date') || $errors->has('datetime') ? ' invalid' : '' }}" type="text" pattern="^20[0-9]{2}/(0[1-9]|1[0-2])/(0[1-9]|[12][0-9]|3[01])$" value="{{ old('date') ?? date('Y/m/d') }}" required>
                        <label for="date">日付</label>

                        @if ($errors->has('date'))
                            <span class="red-text"><strong>{{ $errors->first('date') }}</strong></span>
                        @endif
                    </div>
                    <div class="input-field col s6">
                        <i class="material-icons prefix">schedule</i>
                        <input id="time" name="time" class="validate{{ $errors->has('time') || $errors->has('datetime') ? ' invalid' : '' }}" type="text" pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$" value="{{ old('time') ?? date('H:i') }}">
                        <label for="time">時刻</label>

                        @if ($errors->has('time'))
                            <span class="red-text"><strong>{{ $errors->first('time') }}</strong></span>
                        @endif
                    </div>
                    @if ($errors->has('datetime'))
                    <div class="col s12">
                        <span class="red-text"><strong>{{ $errors->first('datetime') }}</strong></span>
                    </div>
                    @endif
                    <div class="input-field col s12">
                        <i class="material-icons prefix">label</i>
                        <input id="tags" type="text" disabled placeholder="未実装です">
                        <label for="tags">タグ</label>
                    </div>
                    {{--<div class="input-field col s12">--}}
                         {{--TODO: Material Chipsデータのシリアライズとかをjsで書いておく必要あるかも？ --}}
                        {{--<i class="material-icons prefix">label</i>--}}
                        {{--<div class="chips"></div>--}}
                        {{--<label>タグ</label>--}}
                    {{--</div>--}}
                    <div class="input-field col s12">
                        <i class="material-icons prefix">comment</i>
                        <textarea id="note" name="note" class="materialize-textarea{{ $errors->has('note') ? ' invalid' : '' }}" data-length="500">{{ old('note') }}</textarea>
                        <label for="note">ノート</label>

                        @if ($errors->has('note'))
                            <span class="red-text"><strong>{{ $errors->first('note') }}</strong></span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <h6 class="grey-text">オプション</h6>
                        <p>
                            <input id="is-private" name="is_private" class="filled-in" type="checkbox" {{ old('is_private') ? 'checked' : '' }}>
                            <label for="is-private">チェックイン履歴を非公開にする</label>
                        </p>
                    </div>
                </div>
                <div class="row center">
                    <div class="input-field col s12">
                        <button id="submit" class="btn waves-effect waves-light teal lighten-2" type="submit">チェックイン</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function() {
        $('#note').characterCounter();
//        $('.chips').material_chip();
    });
</script>
@endsection