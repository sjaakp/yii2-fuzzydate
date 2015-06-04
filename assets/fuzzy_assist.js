/*global jQuery, console */
/*jslint nomen: true, unparam: true, white: true */
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 04-06-2015.
 */

function fuzzyEnable(elmt_id, bEnable)  {
    "use strict";

    var elmt = $('#' + elmt_id);
    if (bEnable)    {
        elmt.removeAttr('disabled');
    }
    else    {
        elmt.val('');
        elmt.attr('disabled', 'disabled');
    }
}

function fuzzySet(base_id, y, m, d) {
    "use strict";

    if (y)  {
        fuzzyEnable(base_id + '-m', true);

        if (m)  {
            var min_date = new Date(y, m - 1, 1),
                max_date = new Date(y, m, 0),
                max_day = max_date.getDate(),
                curr_date = d ? new Date(y, m - 1, d > max_day ? max_day : d) : null;

            fuzzyEnable(base_id + '-d', true);

            $('#' + base_id + '-d').datepicker('option', {
                minDate: min_date,
                maxDate: max_date,
                defaultDate: curr_date
            })
                .datepicker('setDate', curr_date)
            ;
        }
        else    {
            fuzzyEnable(base_id + '-d', false);
        }
    }
    else    {
        fuzzyEnable(base_id + '-m', false);
        fuzzyEnable(base_id + '-d', false);
    }
}

function fuzzyUpd(elmt, yy)    {
    "use strict";

    var base_id = elmt.id.slice(0, -2),
        y = yy || $('#' + base_id + '-y').val(),
        m = $('#' + base_id + '-m').val(),
        d = $('#' + base_id + '-d').val();

    fuzzySet(base_id, y, m, d);
}

function fuzzyReg()  {
    "use strict";

    $('.fuzzy-year').on('input spin',function(e, ui) {
        fuzzyUpd(this, ui.value);
    })
        .keydown( function(e) {
            if (e.which === 8 || e.which === 46) {
                $(this).val('');
                fuzzyUpd(this);
            }
        }
    );
    $('.fuzzy-month').change(function(e) {
        fuzzyUpd(this);
    });
    $('.fuzzy-day').keydown( function(e) {
        if (e.which === 8 || e.which === 46)  {
            $(this).datepicker('setDate',null)
                .datepicker('option','defaultDate',null);
        }
    });
}
