import $ from 'jquery';

$('#deactivate-form').on('submit', function () {
    if (!confirm('本当にアカウントを削除してもよろしいですか？')) {
        return false;
    }
});
