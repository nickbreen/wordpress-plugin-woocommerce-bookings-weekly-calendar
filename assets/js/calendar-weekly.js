jQuery(function($) {

    $('.week-picker').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        showWeek: true,
        showButtonPanel: true,
        onSelect: function(dateText, inst) {
            var date = $(this).datepicker("getDate");
            date.setDate(date.getDate() - date.getDay() + parseInt(inst.settings.firstDay));
            $(this).datepicker("setDate", date);
        },
        onClose: function () {
            this.blur();
        },
        beforeShow: function (input, inst) {
            var X = [];
            for (var x in input.dataset) {
                var m = x.match(/^datepicker\.(.+)$/);
                if (m)
                    X[m[1]] = input.dataset[m[0]];
            }
            return X;
        }
    });

    $('.ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('.ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });

});
