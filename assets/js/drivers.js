jQuery(function($) {

    var ajaxDefaults = {
        headers: {
            'X-WP-Nonce': wpApiSettings.nonce
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("%s; %s", textStatus, errorThrown);
        },
        beforeSend: function (jqXHR, settings) {
            console.dir(settings.data)
        },
        complete: function (jqXHR, textStatus) {
            console.info(textStatus);
        },
        success: function (data, textStatus, jqXHR) {
            console.dir(data);
        },
    }

    $('table.wc_bookings_calendar_weekly td.booked').droppable({
        accept: 'ul.drivers li',
        classes: {
            "ui-droppable-hover": "ui-state-hover"
        },
        hoverClass: "ui-state-hover", // pre 1.12.0
        drop: function (event, ui) {
            if (!ui.draggable.data('driver')) return
            var url = wpApiSettings.root + 'wp/v2/driver/' + ui.draggable.data('driver').id
            var bookings = $.merge(
                $.isArray(ui.draggable.data('driver').bookings) ? ui.draggable.data('driver').bookings : [],
                $(this).find('li.booking').map(function (i, e) {
                    return parseInt(this.dataset.bookingId);
                }).get()
            )
            $.ajax(url, $.extend({}, ajaxDefaults, {
                method: 'PATCH',
                contentType: 'application/json; charset=UTF-8',
                processData: false,
                data: JSON.stringify({
                    status: 'private',
                    'bookings': bookings
                }),
            }))
        },
    })

    $.ajax(wpApiSettings.root + 'wp/v2/driver', $.extend({}, ajaxDefaults, {
        success: [
            function (data, textStatus, jqXHR) {
                $('<tfoot><tr><th>Drivers<td colspan="7"><ul class="drivers">').appendTo('table.wc_bookings_calendar_weekly')
                $('<li>').text("no driver")
                    .appendTo('ul.drivers').after(
                        $.map(data, function (e, i) {
                            return $('<li>').addClass('driver')
                                .text(e.title.rendered)
                                .data('driver', e)
                                .css('background-color', $.Color(e.colour).lightness(0.75))
                        })
                    )
                    .siblings().addBack().draggable({helper: 'clone', opacity: 0.7})
            },
            function (data, textStatus, jqXHR) {
                data.filter(function (e, i) {
                    return e.bookings
                }).map(function (e, i) {
                    $('td.booked').filter(function (j, f) {
                        return $('li.booking', this).filter(function (k, g) {
                            return e.bookings.includes(parseInt(this.dataset.bookingId))
                        }).length
                    })
                    .css('background-color', $.Color(e.colour).lightness(0.75))
                })
            },
        ]
    }))
});
