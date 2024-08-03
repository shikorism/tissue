import $ from 'jquery';
import { fetchGet, fetchDeleteJson, ResponseError } from './fetch';

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
        .then((response) => {
            if (response.ok) {
                return response.json();
            }
            throw new ResponseError(response);
        })
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

export function checkinMutedWarning(el: Element) {
    el.addEventListener('click', () => {
        el.parentNode?.querySelector('.tis-checkin-muted')?.classList?.remove('tis-checkin-muted');
        el.remove();
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
    let element: Element;
    let id: any = null;
    modal.querySelector('form')?.addEventListener('submit', function (event) {
        event.preventDefault();
        const buttons = modal.querySelectorAll('button');
        buttons.forEach((button) => (button.disabled = true));

        const inCheckinPage = location.pathname.startsWith('/checkin/');
        fetchDeleteJson(`/api/checkin/${id}`, {
            flash: inCheckinPage,
        })
            .then((response) => {
                if (response.ok) {
                    return response.json();
                }
                throw new ResponseError(response);
            })
            .then((data) => {
                $(modal).modal('hide');
                if (inCheckinPage) {
                    location.href = data.user || '/';
                } else {
                    element.remove();
                }
            })
            .catch((e) => {
                console.error(e);
                alert('削除中にエラーが発生しました。');
            })
            .finally(() => {
                buttons.forEach((button) => (button.disabled = false));
            });
    });
    return $(modal).on('show.bs.modal', function (event) {
        const target = event.relatedTarget || die();
        element = target.parentElement?.parentElement || die();
        const dateLabel = this.querySelector('.modal-body .date-label') || die();
        dateLabel.textContent = target.dataset.date || null;
        id = target.dataset.id;
    });
}

const THEME_COLORS = ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark'] as const;
type ThemeColor = (typeof THEME_COLORS)[number];
export function showToast(message: string, options: Partial<{ color: ThemeColor; delay: number }> = {}) {
    const $toast = $('.toast');
    $toast.removeClass(THEME_COLORS.map((color) => `tis-toast-${color}`));
    if (options.color) {
        $toast.addClass(`tis-toast-${options.color}`);
    }
    $toast.find('.toast-body').text(message);
    $toast.toast({ delay: options.delay || 5000 }).toast('show');
}
