jQuery(function($) {

    $('table.wc_bookings_calendar_weekly td.booked').droppable({
        accept: 'ul.drivers li',
        classes: {
            "ui-droppable-hover": "ui-state-hover"
        },
        hoverClass: "ui-state-hover", // pre 1.12.0
        drop: function (event, ui) {
            console.log(this)
            var driver = ui.draggable.get(0).dataset.driverId
            var bookings = $.map($(this).find('li.booking').get(), function (e, i) {
                return e.dataset.bookingId;
            })
            $.ajax(wpApiSettings.root + 'wp/v2/driver', $.extend({}, ajaxDefaults, {
                method: 'POST',
                contentType: 'application/json; charset=UTF-8',
                dataType: 'json',
                data: {
                    user: driver,
                    bookings: bookings
                },
            }))
        },
    })

    var ajaxDefaults = {
        headers: {
            'X-WP-Nonce': wpApiSettings.nonce
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("%s; %s", textStatus, errorThrown);
        },
    }

    $.ajax(wpApiSettings.root + 'wp/v2/driver', $.extend({}, ajaxDefaults, {
        success: [
            function (data, textStatus, jqXHR) {
                $('<tfoot><tr><th>Drivers<td colspan="7"><ul class="drivers">').appendTo('table.wc_bookings_calendar_weekly')
                $('<li>').text("no driver")
                    .appendTo('ul.drivers').after(
                        $.map(data, function (e, i) {
                            return $('<li>').addClass('driver')
                                .text(e.title.rendered)
                                .data(e)
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
