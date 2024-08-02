import $ from 'jquery';

$('#protected').on('change', function () {
    if (!$(this).prop('checked')) {
        alert(
            'チェックイン履歴を公開に切り替えると、個別に非公開設定されているものを除いた全てのチェックインが誰でも閲覧できるようになります。\nご注意ください。',
        );
    }
});
