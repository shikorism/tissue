(function ($) {

    $.fn.linkCard = function (options) {
        const settings = $.extend({
            endpoint: '/api/checkin/card'
        }, options);

        return this.each(function () {
            const $this = $(this);
            $.ajax({
                url: settings.endpoint,
                method: 'get',
                type: 'json',
                data: {
                    url: $this.find('a').attr('href')
                }
            }).then(function (data) {
                const $metaColumn = $this.find('.col-12:last-of-type');
                const $imageColumn = $this.find('.col-12:first-of-type');
                const $title = $this.find('.card-title');
                const $desc = $this.find('.card-text');
                const $image = $imageColumn.find('img');

                if (data.title === '') {
                    $title.hide();
                } else {
                    $title.text(data.title);
                }

                if (data.description === '') {
                    $desc.hide();
                } else {
                    $desc.text(data.description);
                }

                if (data.image === '') {
                    $imageColumn.hide();
                    $metaColumn.removeClass('col-md-6');
                } else {
                    $image.attr('src', data.image);
                }

                if (data.title !== '' || data.description !== '' || data.image !== '') {
                    $this.removeClass('d-none');
                }
            });
        });
    };

    $.fn.pageSelector = function () {
        return this.on('change', function () {
            location.href = $(this).find(':selected').data('href');
        });
    };

    $.fn.deleteCheckinModal = function () {
        return this.each(function () {
            $(this).on('show.bs.modal', function (event) {
                const target = $(event.relatedTarget!);
                const modal = $(this);
                modal.find('.modal-body .date-label').text(target.data('date'));
                modal.data('id', target.data('id'));
            }).find('.btn-danger').on('click', function (event) {
                const modal = $('#deleteCheckinModal');
                const form = modal.find('form');
                form.attr('action', form.attr('action')?.replace('@', modal.data('id')) || null);
                form.submit();
            })
        });
    };

})(jQuery);
