// jQuery UI date picker to pick a week
jQuery(function($) {

    $('.week-picker').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) {
            // $(this.form).submit();
            console.log(dateText, inst);
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

    $('.week-picker .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('.week-picker .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
});
