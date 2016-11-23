jQuery(function($) {

    var ajaxDefaults = {
        headers: {
            'X-WP-Nonce': wpApiSettings.nonce
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("%s; %s", textStatus, errorThrown);
        },
        complete: function (jqXHR, textStatus) {
            console.debug(textStatus);
        },
    }

    $('table.wc_bookings_calendar_weekly td.booked').droppable({
        accept: 'ul.drivers li',
        classes: {
            "ui-droppable-hover": "ui-state-hover"
        },
        hoverClass: "ui-state-hover", // pre 1.12.0
        drop: function (event, ui) {
            var bookings = $.merge(
                $.isArray(ui.draggable.data('driver').bookings) ? ui.draggable.data('driver').bookings : [],
                $('li.booking', this).map(function (i, e) {
                    return parseInt(this.dataset.bookingId);
                }).get()
            )
            $.ajax(wpApiSettings.root + 'wp/v2/driver/' + ui.draggable.data('driver').id, $.extend({}, ajaxDefaults, {
                context: $('li.booking', this),
                method: 'PATCH',
                contentType: 'application/json; charset=UTF-8',
                processData: false,
                data: JSON.stringify({'bookings': bookings}),
                success: function (data, textStatus, jqXHR) {
                    this.each(function (i, e) {
                        if ($('.driver', this).map(function () {
                                return $(this).data('driverId')
                            }).get().includes(ui.draggable.data('driver').id)) return
                        $('<span>').appendTo(e)
                            .text(ui.draggable.data('driver').title.rendered)
                            .attr('title', "Click to unassign " + e.title.rendered + " from this booking")
                            .addClass('driver')
                            .data('driverId', ui.draggable.data('driver').id)
                            .css('background-color', $.Color(ui.draggable.data('driver').colour).lightness(0.75))
                    })
                },
            }))
        },
    }).on('click', '.driver', function (e) {
        $.ajax(wpApiSettings.root + 'wp/v2/wc_booking/' + $(this).closest('li.booking').data('bookingId'), $.extend({}, ajaxDefaults, {
            context: this,
            method: 'PATCH',
            contentType: 'application/json; charset=UTF-8',
            processData: false,
            data: JSON.stringify({
                'drivers': $(this).siblings().map(function () {
                    return $(this).data('driverId')
                }).get()
            }),
            beforeSend: function (jqXHR, settings) {
                console.log(settings)
            },
            success: function (data, textStatus, jqXHR) {
                this.remove()
            },
        }))
    })

    $.ajax(wpApiSettings.root + 'wp/v2/wc_booking', $.extend({}, ajaxDefaults, {
        context: $('td.booked li.booking'),
        data: {
            include: $('td.booked li.booking').map(function (i, e) {
                return this.dataset.bookingId
            }).get(),
            status: '*',
        },
        success: [
            function (data, textStatus, jqXHR) {
                this.each(function (i, e) {
                    $(this).data('drivers', data.filter(function (f, j, a) {
                            return f.id == e.dataset.bookingId
                        }).map(function (f, j, a) {
                            return f.drivers
                        }).reduce(function (a, b) {
                            return a.concat(b)
                        }, []).forEach(function (f, j, a) {
                            $('<span>').appendTo(e)
                                .text(f.post_title)
                                .attr('title', "Click to unassign " + f.post_title + " from this booking")
                                .addClass('driver')
                                .data('driverId', f.id)
                                .css('background-color', $.Color(f.colour).lightness(0.75))
                        })
                    )
                })
            },
        ]
    }))

    $.ajax(wpApiSettings.root + 'wp/v2/driver', $.extend({}, ajaxDefaults, {
        data: {
            status: '*'
        },
        success: [
            function (data, textStatus, jqXHR) {
                $('<tfoot><tr><th colspan="8">Drag&apos;n&apos;drop to assign a driver to a booking<ul class="drivers">').appendTo('table.wc_bookings_calendar_weekly')
                    .find('ul.drivers').append(
                        $.map(data, function (e, i) {
                            return $('<li>').addClass('driver')
                                .text(e.title.rendered)
                                .attr('title', "Drag'n'drop to assign " + e.title.rendered + " to a booking")
                                .data('driver', e)
                                .css('background-color', $.Color(e.colour).lightness(0.75))
                        })
                    )
                    .find('li').draggable({helper: 'clone', opacity: 0.7})
            },
        ]
    }))
});
