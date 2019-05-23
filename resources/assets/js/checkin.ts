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

function insertTag(value: string) {
    $('<li class="list-inline-item badge badge-primary" style="cursor: pointer;"><span class="oi oi-tag"></span> <span></span> | x</li>')
        .data('value', value)
        .children(':last-child')
            .text(value)
            .end()
        .appendTo('#tags');
}

const initTags = $('input[name=tags]').val() as string;
if (initTags.trim() !== '') {
    initTags.split(' ').forEach(function (value) {
        insertTag(value);
    });
}

$('#tagInput').on('keydown', function (ev: JQuery.KeyDownEvent) {
    const $this = $(this);
    let value = $this.val() as string;
    if (value.trim() !== '') {
        switch (ev.key) {
            case 'Tab':
            case 'Enter':
            case ' ':
                if ((ev.originalEvent as any).isComposing !== true) {
                    insertTag(value.trim());
                    $this.val('');
                    updateTags();
                }
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