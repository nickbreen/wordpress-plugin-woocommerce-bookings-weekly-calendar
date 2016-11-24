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
            $('li.booking', this).each(function () {
                $.ajax(wpApiSettings.root + 'wp/v2/wc_booking/' + this.dataset.bookingId, $.extend({}, ajaxDefaults, {
                    context: this,
                    method: 'GET',
                    success: function (data, textStatus, jqXHR) {
                        $.ajax(wpApiSettings.root + 'wp/v2/wc_booking/' + this.dataset.bookingId, $.extend({}, ajaxDefaults, {
                            context: this,
                            method: 'PATCH',
                            contentType: 'application/json; charset=UTF-8',
                            processData: false,
                            data: JSON.stringify({
                                drivers: [ui.draggable.prop('dataset').driverId].concat(data.drivers ? data.drivers.map(function (e, i, a) {
                                        return e.id
                                    }) : [])
                                }),
                            beforeSend: console.log,
                            success: function (data, textStatus, jqXHR) {
                                $('<span>').appendTo(this)
                                    .text(ui.draggable.text())
                                    .attr('title', "Click to unassign " + ui.draggable.text() + " from this booking")
                                    .addClass('driver')
                                    .attr('data-driver-id', ui.draggable.prop('dataset').driverId)
                                    .css({
                                        'background-color': ui.draggable.css('background-color'),
                                        'border-color': ui.draggable.css('border-color'),
                                    });
                            },
                        }))
                    },
                }))
            })
        },
    }).on('click', '.driver', function (event) {
        $(this).closest('li.booking').each(function (i, e) {
            $.ajax(wpApiSettings.root + 'wp/v2/wc_booking/' + this.dataset.bookingId, $.extend({}, ajaxDefaults, {
                context: this,
                method: 'GET',
                success: function (data, textStatus, jqXHR) {
                    $.ajax(wpApiSettings.root + 'wp/v2/wc_booking/' + this.dataset.bookingId, $.extend({}, ajaxDefaults, {
                        context: this,
                        method: 'PATCH',
                        contentType: 'application/json; charset=UTF-8',
                        processData: false,
                        data: JSON.stringify({drivers: data.drivers.map(function (e, i, a) {
                            return e.id
                        }).filter(function (e, i, a) {
                            return e != parseInt(event.target.dataset.driverId)
                        })}),
                        beforeSend: console.log,
                        success: function (data, textStatus, jqXHR) {
                            $(event.target).remove()
                        },
                    }))
                },
            }))
        })
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
                    data.filter(function (f, j, a) {
                                return f.id == e.dataset.bookingId && f.drivers
                            })
                        .map(function (f, j, a) {
                                return f.drivers
                            })
                        .reduce(function (a, b) {
                                return a.concat(b)
                            }, [])
                        .sort(function (a, b) {
                                if (a.post_title < b.post_title) return -1
                                if (a.post_title > b.post_title) return 1
                                return 0
                            })
                        .forEach(function (f, j, a) {
                                $('<span>').appendTo(e)
                                    .text(f.post_title)
                                    .attr('title', "Click to unassign " + f.post_title + " from this booking")
                                    .addClass('driver')
                                    .attr('data-driver-id', f.id)
                                    .css({
                                        'background-color': $.Color(f.colour).lightness(0.75),
                                        'border-color': $.Color(f.colour).lightness(0.5),
                                    })
                            })
                })
            },
        ]
    }))

    $.ajax(wpApiSettings.root + 'wp/v2/driver', $.extend({}, ajaxDefaults, {
        data: {
            status: '*',
            per_page: 100,
            order: 'asc',
            order_by: 'title'
        },
        success: [
            function (data, textStatus, jqXHR) {
                $('<tfoot><tr><th colspan="8">Drag&apos;n&apos;drop to assign a driver to a booking<ul class="drivers">').appendTo('table.wc_bookings_calendar_weekly')
                    .find('ul.drivers').append(
                        $.map(data, function (e, i) {
                            return $('<li>').addClass('driver')
                                .text(e.title.rendered)
                                .attr('title', "Drag'n'drop to assign " + e.title.rendered + " to a booking")
                                .attr('data-driver-id', e.id)
                                .css({
                                    'background-color': $.Color(e.colour).lightness(0.75),
                                    'border-color': $.Color(e.colour).lightness(0.5),
                                })
                        })
                    )
                    .find('li').draggable({helper: 'clone', opacity: 0.7})
            },
        ]
    }))
});
