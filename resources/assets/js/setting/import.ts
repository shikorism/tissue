import $ from 'jquery';

$('#destroy-form').on('submit', function () {
    if (!confirm('本当に削除してもよろしいですか？')) {
        return false;
    }
});
