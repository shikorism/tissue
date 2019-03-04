function updateTags() {
    $('input[name=tags]').val(
        $('#tags')
            .find('li')
            .map(function () {
                return $(this).data('value');
            })
            .get()
            .join(' ')
    );
}

function insertTag(value) {
    $('#tags').append('<li class="list-inline-item badge badge-primary" style="cursor: pointer;" data-value="' + value + '"><span class="oi oi-tag"></span> ' + value + ' | x</li>');
}

var initTags = $('input[name=tags]').val();
if (initTags.trim() !== '') {
    initTags.split(' ').forEach(function (value) {
        insertTag(value);
    });
}

$('#tagInput').on('keydown', function (ev) {
    var $this = $(this);
    if ($this.val().trim() !== '') {
        switch (ev.key) {
            case 'Tab':
            case 'Enter':
            case ' ':
                insertTag($this.val().trim());
                $this.val('');
                updateTags();
                ev.preventDefault();
                break;
        }
    } else if (ev.key === 'Enter') {
        // 誤爆防止
        ev.preventDefault();
    }
});

$('#tags')
    .on('click', 'li', function (ev) {
        $(this).remove();
        updateTags();
    })
    .parent()
    .on('click', function (ev) {
        $('#tagInput').focus();
    });