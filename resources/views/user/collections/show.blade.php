@extends('layouts.base')

@section('title', $user->display_name . ' さんのコレクション')

@push('head')
@endpush

@section('content')
    @component('user.components.header', ['user' => $user])
    @endcomponent
    <div id="app"></div>

    {{-- TODO: Modal制御をReactに移して消す --}}
    @component('components.modal', ['id' => 'deleteCollectionItemModal'])
        @slot('title')
            削除確認
        @endslot
        <a class="link-label" target="_blank" rel="noopener"></a> をコレクションから削除してもよろしいですか？
        @slot('footer')
            <form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <button type="submit" class="btn btn-danger">削除</button>
            </form>
        @endslot
    @endcomponent
@endsection

@push('script')
    <script src="{{ mix('js/user/collections.js') }}"></script>
@endpush

