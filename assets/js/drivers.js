jQuery(function($) {

    var me = null;
    var drivers = [];
    var driverBookings = [];
    var bookings = $('li.booking');

    $('table.wc_bookings_calendar_weekly td.booked').droppable({
        accept: 'ul.drivers li',
        classes: {
            "ui-droppable-hover": "ui-state-hover"
        },
        hoverClass: "ui-state-hover", // pre 1.12.0
        drop: function (event, ui) {
            var driver = ui.draggable.get(0).dataset.driverId
            var bookings = $.map($(this).find('li').get(), function (e, i) {
                return e.dataset.bookingId;
            })
            $.ajax(wpApiSettings.root + 'wp/v2/driver', {
                method: 'POST',
                contentType: 'application/json; charset=UTF-8',
                dataType: 'json',
                data: {
                    user: driver,
                    bookings: bookings
                },
            })
        },
    })

    $.ajaxSetup({
        headers: {
            'X-WP-Nonce': wpApiSettings.nonce
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("%s; %s", textStatus, errorThrown);
        },
        complete: function (jqXHR, textStatus) {
            console.dir({driverBookings:driverBookings, drivers:drivers, bookings:bookings});

            for (i in driverBookings) {
                bookings.filter(function (j, e) {
                    // console.log(parseInt(this.dataset.bookingId), driverBookings[i].bookings, driverBookings[i].bookings.indexOf(parseInt(this.dataset.bookingId)))
                    return -1 != driverBookings[i].bookings.indexOf(parseInt(this.dataset.bookingId))
                })
                .closest('td')
                .filter(function () {
                    return driverBookings[i].user in drivers
                })
                .css('background-color', drivers[driverBookings[i].user]['background-color'])
            }
        },
    });

    $.ajax(wpApiSettings.root + 'wp/v2/users', {
        data: {
            roles: 'driver',
            context: 'edit',
            orderby: 'name'
        },
        success: [
            function (data, textStatus, jqXHR) {
                for (i in data) {
                    var r = 360*5*i/12;
                    data[i]['background-color'] = 'hsla('+r+', 50%, 50%, 0.25)';
                }
            },
            function (data, textStatus, jqXHR) {
                for (i in data)
                    drivers[data[i].id] = data[i]
            },
            function (data, textStatus, jqXHR) {
                $('<tfoot><tr><th>Drivers<td colspan="7"><ul class="drivers">').appendTo('table.wc_bookings_calendar_weekly')

                $('<li>').appendTo('ul.drivers').text("no driver").after(
                    $.map(data, function (element, i) {
                        return $('<li>')
                            .text(element.name)
                            .data(element)
                            .attr('data-driver-id', element.id)
                            .css('background-color', element['background-color'])
                    })
                ).siblings('li').addBack().draggable({helper: 'clone', opacity: 0.7})
            }
        ]
    });

    $.ajax(wpApiSettings.root + 'wp/v2/driver', {
        success: function (data, textStatus, jqXHR) {
            driverBookings = data;
        }
    })

});
