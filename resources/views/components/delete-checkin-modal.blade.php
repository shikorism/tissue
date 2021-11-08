@component('components.modal', ['id' => 'deleteCheckinModal'])
    @slot('title')
        削除確認
    @endslot
    <span class="date-label"></span> のチェックインを削除してもよろしいですか？
    @slot('footer')
        <form>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
            <button type="submit" class="btn btn-danger">削除</button>
        </form>
    @endslot
@endcomponent
