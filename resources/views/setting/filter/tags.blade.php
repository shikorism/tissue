@extends('setting.base')

@section('title', 'タグミュート設定')

@section('tab-content')
    <h3>タグミュート設定</h3>
    <hr>
    <p>指定したタグが含まれるチェックインを非表示にすることができます。</p>
    <h4 class="mt-5">設定を追加</h4>
    <div class="card mt-3">
        <div class="card-body">
{{--            @if (1 >= 100)--}}
{{--                <p class="my-0 text-danger">登録可能なミュート設定は{{ 100 }}件までに制限されています。</p>--}}
{{--            @else--}}
                <form action="" method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="name">タグ名</label>
                        <input id="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" type="text" required autocomplete="off">
                        <small class="form-text text-muted">
                            完全一致ですが、タグ検索と同様のルールで判定します。<br>
                            ・アルファベットは大小文字と半角全角の違いを無視します。<br>
                            ・カタカナは半角全角の違いを無視します。
                        </small>
                        @if ($errors->has('name'))
                            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="mb-2">隠し方</div>
                    <div class="form-group">
                        <div class="custom-control custom-radio">
                            <input id="modeMask" class="custom-control-input" type="radio" name="mode" value="mask" checked>
                            <label for="modeMask" class="custom-control-label">内容を非表示にする</label>
                        </div>
                        <small class="form-text text-muted">
                            オカズやノートの部分を隠した状態にします。クリックすることで内容を見ることができます。
                        </small>
                        <div class="custom-control custom-radio mt-2">
                            <input id="modeRemove" class="custom-control-input" type="radio" name="mode" value="remove">
                            <label for="modeRemove" class="custom-control-label">タイムライン・検索結果から除外</label>
                        </div>
                        <small class="form-text text-muted">
                            お惣菜コーナーや個人ページ、検索画面の各リスト上から完全に除外します。チェックインの個別ページを直接開いた時は警告が表示されます。
                        </small>
                    </div>
                    <button class="btn btn-primary" type="submit">追加</button>
                </form>
{{--            @endif--}}
        </div>
    </div>
    <h4 class="mt-5">現在の設定</h4>
@endsection

@push('script')
@endpush
