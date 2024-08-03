import $ from 'jquery';

const deleteModal = document.getElementById('deleteTagFilterModal');
if (deleteModal) {
    let id: any = null;
    deleteModal.querySelector('form')?.addEventListener('submit', function () {
        this.action = this.action.replace('@', id);
    });
    document.querySelectorAll<HTMLElement>('[data-target="#deleteTagFilterModal"]').forEach((el) => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            id = this.dataset.id;
            $(deleteModal).modal('show', this);
        });
    });
}
