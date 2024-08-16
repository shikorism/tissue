import Cookies from 'js-cookie';
import $ from 'jquery';
import { fetchPostJson, fetchDeleteJson, ResponseError } from './fetch';
import { linkCard, pageSelector, deleteCheckinModal, checkinMutedWarning } from './tissue';
import { initAddToCollectionButtons } from './collection';
import 'bootstrap';

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
                    $this.find('.ti-heart-filled').removeClass('text-danger');

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
                    $this.find('.ti-heart-filled').addClass('text-danger');

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

    $(document).on('click', '.card-spoiler-img-overlay', function (event) {
        event.preventDefault();
        const $this = $(this);
        $this.closest('.card-link').removeClass('card-spoiler');
        $this.remove();
    });

    initAddToCollectionButtons();
});
