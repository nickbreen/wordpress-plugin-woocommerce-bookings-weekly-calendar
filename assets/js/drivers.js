jQuery(function($) {

    var me = null;
    var drivers = [];
    var driverBookings = [];
    var bookings = $('.booking');

    $('table.wc_bookings_calendar_weekly td.booked').droppable({
        accept: 'ul.drivers li',
        classes: {
            "ui-droppable-hover": "ui-state-hover"
        },
        hoverClass: "ui-state-hover", // pre 1.12.0
        drop: function (event, ui) {
            console.log(event, ui,

            ui.draggable.get(0).dataset,
            $(this).find('li')
        )
            // AJAX to add or remove assignment
            $(this).find('li')
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
            // console.dir([driverBookings, drivers, bookings]);
        }
    });

    $.ajax(wpApiSettings.root + 'wp/v2/users/me', {
        success: function (data, textStatus, jqXHR) {
            me = data;
        }
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
                drivers = data;
            },
            function (data, textStatus, jqXHR) {
                $("<h1>").text("Drivers").appendTo('#mainform')
                $('<ul>').addClass('drivers')
                    .append($('<li>').text("no driver"))
                    .append(
                        $.map(data, function (element, i) {
                            return $('<li>')
                                .text(element.name)
                                .css('background-color', element['background-color'])
                                .data(element)
                                .attr('data-driver-id', element.id)
                        })
                    ).appendTo('#mainform').children('li').draggable({helper: 'clone', opacity: 0.7})
            }
        ]
    });

    $.ajax(wpApiSettings.root + 'wp/v2/driver', {
        success: function (data, textStatus, jqXHR) {
            driverBookings = data;
        }
    })

});
