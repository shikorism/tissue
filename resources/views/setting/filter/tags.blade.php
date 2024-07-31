@extends('setting.base')

@section('title', 'タグミュート設定')

@section('tab-content')
    <h3>タグミュート設定</h3>
    <hr>
    <p>指定したタグが含まれるチェックインを非表示にすることができます。</p>
    <h4 class="mt-5">設定を追加</h4>
    <div class="card mt-3">
        <div class="card-body">
            @if (count($tagFilters) >= $perUserLimit)
                <p class="my-0 text-danger">登録可能なミュート設定は{{ $perUserLimit }}件までに制限されています。</p>
            @else
                <form action="{{ route('setting.filter.tags.store') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="name">タグ名</label>
                        <input id="name" class="form-control {{ $errors->has('tag_name') ? ' is-invalid' : '' }}" name="tag_name" type="text" required autocomplete="off">
                        <small class="form-text text-muted">
                            完全一致ですが、タグ検索と同様のルールで判定します。<br>
                            ・アルファベットは大小文字と半角全角の違いを無視します。<br>
                            ・カタカナは半角全角の違いを無視します。
                        </small>
                        @if ($errors->has('tag_name'))
                            <div class="invalid-feedback">{{ $errors->first('tag_name') }}</div>
                        @endif
                    </div>
                    <div class="mb-2">隠し方</div>
                    <div class="form-group">
                        <div class="custom-control custom-radio">
                            <input id="modeMask" class="custom-control-input" type="radio" name="mode" value="1" checked>
                            <label for="modeMask" class="custom-control-label">内容を非表示にする</label>
                        </div>
                        <small class="form-text text-muted">
                            オカズやノートの部分を隠した状態にします。クリックすることで内容を見ることができます。
                        </small>
                        <div class="custom-control custom-radio mt-2">
                            <input id="modeRemove" class="custom-control-input" type="radio" name="mode" value="2">
                            <label for="modeRemove" class="custom-control-label">タイムライン・検索結果から除外</label>
                        </div>
                        <small class="form-text text-muted">
                            お惣菜コーナーや個人ページ、検索画面の各リスト上から完全に除外します。チェックインの個別ページを直接開いた時は警告が表示されます。
                        </small>
                    </div>
                    @if ($errors->has('mode'))
                        <div class="invalid-feedback">{{ $errors->first('mode') }}</div>
                    @endif
                    <button class="btn btn-primary" type="submit">追加</button>
                </form>
            @endif
        </div>
    </div>
    @if (!$tagFilters->isEmpty())
        <h4 class="mt-5">現在の設定</h4>
        <div class="list-group mt-3">
            @foreach ($tagFilters as $tagFilter)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1 mr-2">
                        <div><i class="ti ti-tag text-secondary"></i> {{ $tagFilter->tag_name }}</div>
                        <small class="text-muted">
                            @switch ($tagFilter->mode)
                                @case (\App\TagFilter::MODE_MASK)
                                    内容を非表示
                                    @break
                                @case (\App\TagFilter::MODE_REMOVE)
                                    タイムライン・検索結果から除外
                                    @break
                            @endswitch
                        </small>
                    </div>
                    <div class="ml-2">
                        <button class="btn btn-outline-danger" type="button" data-target="#deleteTagFilterModal" data-id="{{ $tagFilter->id }}">削除</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @component('components.modal', ['id' => 'deleteTagFilterModal'])
        @slot('title')
            削除確認
        @endslot
        ミュート設定を削除してもよろしいですか？
        @slot('footer')
            <form action="{{ route('setting.filter.tags.destroy', ['tag_filter' => '@']) }}" method="post">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <button type="submit" class="btn btn-danger">削除</button>
            </form>
        @endslot
    @endcomponent
@endsection

@push('script')
    @vite('resources/assets/js/setting/filter/tags.ts')
@endpush
