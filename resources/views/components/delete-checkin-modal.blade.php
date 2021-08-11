@component('components.modal', ['id' => 'deleteCheckinModal'])
    @slot('title')
        削除確認
    @endslot
    <span class="date-label"></span> のチェックインを削除してもよろしいですか？
    @slot('footer')
        <form action="{{ route('checkin.destroy', ['id' => '@']) }}" method="post">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
            <button type="submit" class="btn btn-danger">削除</button>
        </form>
    @endslot
@endcomponent
