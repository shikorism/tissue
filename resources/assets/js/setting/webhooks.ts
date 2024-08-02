import ClipboardJS from 'clipboard';
import $ from 'jquery';

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

const deleteModal = document.getElementById('deleteIncomingWebhookModal');
if (deleteModal) {
    let id: any = null;
    deleteModal.querySelector('form')?.addEventListener('submit', function () {
        this.action = this.action.replace('@', id);
    });
    document.querySelectorAll<HTMLElement>('[data-target="#deleteIncomingWebhookModal"]').forEach((el) => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            id = this.dataset.id;
            $(deleteModal).modal('show', this);
        });
    });
}
