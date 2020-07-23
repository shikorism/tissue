import * as ClipboardJS from 'clipboard';

$('.webhook-url').on('focus', function () {
    $(this).trigger('select');
});

new ClipboardJS('.copy-to-clipboard', {
    target(elem: Element): Element {
        return elem.parentElement?.parentElement?.querySelector('.webhook-url') as Element;
    },
}).on('success', (e) => {
    e.clearSelection();
    $(e.trigger).popover('show');
});
$('.copy-to-clipboard').on('shown.bs.popover', function () {
    setTimeout(() => $(this).popover('hide'), 3000);
});

const $deleteModal = $('#deleteIncomingWebhookModal');
$deleteModal.find('.btn-danger').on('click', function () {
    const $form = $deleteModal.find('form');
    $form.attr('action', $form.attr('action')?.replace('@', $deleteModal.data('id')) || null);
    $form.submit();
});
$('[data-target="#deleteIncomingWebhookModal"]').on('click', function (event) {
    event.preventDefault();
    $deleteModal.data('id', $(this).data('id'));
    $deleteModal.modal('show', this);
});
