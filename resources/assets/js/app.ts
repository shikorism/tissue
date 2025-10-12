import Cookies from 'js-cookie';
import $ from 'jquery';
import { linkCard, pageSelector, checkinMutedWarning } from './tissue';
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

    $(document).on('click', '[data-href]', function (_event) {
        location.href = $(this).data('href');
    });

    $(document).on('click', '.card-spoiler-img-overlay', function (event) {
        event.preventDefault();
        const $this = $(this);
        $this.closest('.card-link').removeClass('card-spoiler');
        $this.remove();
    });
});
