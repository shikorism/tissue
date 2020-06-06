import * as Cookies from 'js-cookie';
import jqXHR = JQuery.jqXHR;

require('./bootstrap');

$(() => {
    if (Cookies.get('agechecked')) {
        $('body').removeClass('tis-need-agecheck');
    } else {
        $('#ageCheckModal')
            .modal({backdrop: 'static'})
            .on('hide.bs.modal', function () {
                $('body').removeClass('tis-need-agecheck');
                Cookies.set('agechecked', '1', {expires: 365});
            });
    }

    if (navigator.serviceWorker) {
        navigator.serviceWorker.register('/sw.js');
    }
    $('[data-toggle="tooltip"]').tooltip();
    $('.alert').alert();
    $('.tis-page-selector').pageSelector();

    $('.link-card').linkCard();
    const $deleteCheckinModal = $('#deleteCheckinModal').deleteCheckinModal();
    $(document).on('click', '[data-target="#deleteCheckinModal"]', function (event) {
        event.preventDefault();
        $deleteCheckinModal.modal('show', this);
    });

    $(document).on('click', '[data-href]', function (event) {
        location.href = $(this).data('href');
    });

    $(document).on('click', '.like-button', function (event) {
        event.preventDefault();

        const $this = $(this);
        const targetId = $this.data('id');
        const isLiked = $this.data('liked');

        if (isLiked) {
            const callback = (data: any) => {
                $this.data('liked', false);
                $this.find('.oi-heart').removeClass('text-danger');

                const count = data.ejaculation ? data.ejaculation.likes_count : 0;
                $this.find('.like-count').text(count ? count : '');
            };

            $.ajax({
                url: '/api/likes/' + encodeURIComponent(targetId),
                method: 'delete',
                type: 'json'
            })
                .then(callback)
                .catch(function (xhr: jqXHR) {
                    if (xhr.status === 404) {
                        callback(JSON.parse(xhr.responseText));
                        return;
                    }

                    console.error(xhr);
                    alert('いいねを解除できませんでした。');
                });
        } else {
            const callback = (data: any) => {
                $this.data('liked', true);
                $this.find('.oi-heart').addClass('text-danger');

                const count = data.ejaculation ? data.ejaculation.likes_count : 0;
                $this.find('.like-count').text(count ? count : '');
            };

            $.ajax({
                url: '/api/likes',
                method: 'post',
                type: 'json',
                data: {
                    id: targetId
                }
            })
                .then(callback)
                .catch(function (xhr: jqXHR) {
                    if (xhr.status === 409) {
                        callback(JSON.parse(xhr.responseText));
                        return;
                    } else if (xhr.status === 401) {
                        alert('いいねするためにはログインしてください。');
                        return;
                    }

                    console.error(xhr);
                    alert('いいねできませんでした。');
                });
        }
    });

    $(document).on('click', '.card-spoiler-overlay', function (event) {
        const $this = $(this);
        $this.siblings(".card-link").removeClass("card-spoiler");
        $this.remove();
    });
});
