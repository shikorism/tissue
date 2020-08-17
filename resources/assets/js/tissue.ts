import { fetchGet } from './fetch';

export function suicide<T>(e: T) {
    return function (): never {
        throw e;
    };
}

const die = suicide('Element not found!');

export function linkCard(el: Element) {
    const url = el.querySelector('a')?.href;
    if (!url) {
        return;
    }

    fetchGet('/api/checkin/card', { url })
        .then((response) => response.json())
        .then((data) => {
            const metaColumn = el.querySelector('.col-12:last-of-type') || die();
            const imageColumn = el.querySelector<HTMLElement>('.col-12:first-of-type') || die();
            const title = el.querySelector<HTMLElement>('.card-title') || die();
            const desc = el.querySelector<HTMLElement>('.card-text') || die();
            const image = imageColumn.querySelector('img') || die();

            if (data.title === '') {
                title.style.display = 'none';
            } else {
                title.textContent = data.title;
            }

            if (data.description === '') {
                desc.style.display = 'none';
            } else {
                desc.textContent = data.description;
            }

            if (data.image === '') {
                imageColumn.style.display = 'none';
                metaColumn.classList.remove('col-md-6');
            } else {
                image.src = data.image;
            }

            if (data.title !== '' || data.description !== '' || data.image !== '') {
                el.classList.remove('d-none');
            }
        });

    return el;
}

export function pageSelector(el: Element) {
    if (el instanceof HTMLSelectElement) {
        el.addEventListener('change', function () {
            location.href = this.options[this.selectedIndex].dataset.href as string;
        });
    }
    return el;
}

export function deleteCheckinModal(modal: Element) {
    let id: any = null;
    modal.querySelector('form')?.addEventListener('submit', function () {
        this.action = this.action.replace('@', id);
    });
    return $(modal).on('show.bs.modal', function (event) {
        const target = event.relatedTarget || die();
        const dateLabel = this.querySelector('.modal-body .date-label') || die();
        dateLabel.textContent = target.dataset.date || null;
        id = target.dataset.id;
    });
}
