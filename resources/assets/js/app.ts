import Cookies from 'js-cookie';
import { fetchPostJson, fetchDeleteJson, ResponseError } from './fetch';
import { linkCard, pageSelector, deleteCheckinModal, checkinMutedWarning, showToast } from './tissue';

require('./bootstrap');

$(() => {
    if (Cookies.get('agechecked')) {
        $('body').removeClass('tis-need-agecheck');
    } else {
        $('#ageCheckModal')
            .modal({ backdrop: 'static' })
            .on('hide.bs.modal', function () {
                $('body').removeClass('tis-need-agecheck');
                Cookies.set('agechecked', '1', { expires: 365 });
            });
    }

    if (navigator.serviceWorker) {
        navigator.serviceWorker.register('/sw.js');
    }
    $('[data-toggle="tooltip"], [data-tooltip="tooltip"]').tooltip();
    $('.alert').alert();
    document.querySelectorAll('.tis-page-selector').forEach(pageSelector);

    document.querySelectorAll('.link-card').forEach(linkCard);
    document.querySelectorAll('.tis-checkin-muted-warning').forEach(checkinMutedWarning);

    const elDeleteCheckinModal = document.getElementById('deleteCheckinModal');
    if (elDeleteCheckinModal) {
        const $deleteCheckinModal = deleteCheckinModal(elDeleteCheckinModal);
        $(document).on('click', '[data-target="#deleteCheckinModal"]', function (event) {
            event.preventDefault();
            $deleteCheckinModal.modal('show', this);
        });
    }

    $(document).on('click', '[data-href]', function (_event) {
        location.href = $(this).data('href');
    });

    $(document).on('click', '.like-button', function (event) {
        event.preventDefault();

        const $this = $(this);
        const targetId = $this.data('id');
        const isLiked = $this.data('liked');

        if (isLiked) {
            fetchDeleteJson(`/api/likes/${encodeURIComponent(targetId)}`)
                .then((response) => {
                    if (response.status === 200 || response.status === 404) {
                        return response.json();
                    }
                    throw new ResponseError(response);
                })
                .then((data) => {
                    $this.data('liked', false);
                    $this.find('.oi-heart').removeClass('text-danger');

                    const count = data.ejaculation ? data.ejaculation.likes_count : 0;
                    $this.find('.like-count').text(count ? count : '');
                })
                .catch((e) => {
                    console.error(e);
                    alert('いいねを解除できませんでした。');
                });
        } else {
            fetchPostJson('/api/likes', { id: targetId })
                .then((response) => {
                    if (response.status === 200 || response.status === 409) {
                        return response.json();
                    }
                    throw new ResponseError(response);
                })
                .then((data) => {
                    $this.data('liked', true);
                    $this.find('.oi-heart').addClass('text-danger');

                    const count = data.ejaculation ? data.ejaculation.likes_count : 0;
                    $this.find('.like-count').text(count ? count : '');
                })
                .catch((e) => {
                    if (e instanceof ResponseError && e.response.status === 401) {
                        alert('いいねするためにはログインしてください。');
                        return;
                    }

                    console.error(e);
                    alert('いいねできませんでした。');
                });
        }
    });

    $(document).on('click', '.add-to-collection-button', async function (event) {
        event.preventDefault();

        const $this = $(this);
        const link = $this.data('link');

        try {
            const response = await fetchPostJson('/api/collections/inbox', { link });
            if (response.ok) {
                showToast('あとで抜く に追加しました', { color: 'success', delay: 5000 });
            } else {
                throw new ResponseError(response);
            }
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations && data.error.violations.some((v: any) => v.field === 'link')) {
                    showToast('すでに登録されています', { color: 'danger', delay: 5000 });
                    return;
                }
            }
            showToast('あとで抜く に追加できませんでした', { color: 'danger', delay: 5000 });
        }
    });

    $(document).on('click', '.card-spoiler-img-overlay', function (event) {
        event.preventDefault();
        const $this = $(this);
        $this.closest('.card-link').removeClass('card-spoiler');
        $this.remove();
    });
});
