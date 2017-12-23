/*global jQuery, console */
/*jslint nomen: true, unparam: true, white: true */
/**
 * MIT licence
 * Version 1.1.0
 * Sjaak Priester, Amsterdam 04-06-2015, 19-12-2017.
 */


/**
 *
 * @param elmt_id   element id, prepended by '#'
 * @param bEnable
 */
function fuzzyEnableElement(elmt_id, bEnable)  {
    "use strict";

    var elmt = $(elmt_id);
    if (bEnable)    {
        elmt.removeAttr('disabled');
    }
    else    {
        elmt.val('');
        elmt.attr('disabled', 'disabled');
    }
}

/**
 *
 * @param elmt   input element
 * @param yy    year (hack for first spin step after empty y)
 */
function fuzzyEnable(elmt, yy) {
    "use strict";

    var base_id = '#' + elmt.id.slice(0, -1),
        y = yy || $(base_id + 'y').val(),
        m = $(base_id + 'm').val(),
        d = $(base_id + 'd').val();

    if (!!y)  {
        fuzzyEnableElement(base_id + 'm', true);      // if year not null, enable month
        fuzzyEnableElement(base_id + 'd', !!m);       // and enable day, if month is also not null
    }
    else    {
        fuzzyEnableElement(base_id + 'm', false);     // if year is null disable month
        fuzzyEnableElement(base_id + 'd', false);     // and disable day
    }
}

function fuzzyReg()  {
    "use strict";

    $('.fuzzy-year').on('input spin',function(e, ui) {
        fuzzyEnable(this, ui.value);
    })
        .keydown( function(e) {
            if (e.which === 8 || e.which === 46) {
                $(this).val('');
                fuzzyEnable(this);
            }
        }
    ).each(function(i, elmt) {
        fuzzyEnable(elmt);
    });

    $('.fuzzy-month').change(function(e) {
        fuzzyEnable(this);
    });

    $('.fuzzy-day').keydown( function(e) {
        if (e.which === 8 || e.which === 46)  {
            $(this).val('');
            $('#' + this.id.slice(0, -1) + 'p').datepicker('setDate', null);
        }
    })
        .focus( function(e) {
            var base_id = '#' + this.id.slice(0, -1),
                y = $(base_id + 'y').val(),
                m = $(base_id + 'm').val(),
                d = $(base_id + 'd').val(),
                min_date = new Date(y, m - 1, 1),
                max_date = new Date(y, m, 0),
                max_day = max_date.getDate(),
                curr_date = d ? new Date(y, m - 1, d > max_day ? max_day : d) : null,
                day_elmt = this;

            $(base_id + 'p').datepicker('option', {
                minDate: min_date,
                maxDate: max_date,
                defaultDate: curr_date,
                dateFormat: 'yy-m-d',
                hideIfNoPrevNext: true,

                onSelect: function(d, inst) {
                    var dd = d.split('-');
                    $(day_elmt).val(dd[2]);
                }

            }).datepicker('show');
    });

    $('.fuzzy-today').click(function(e) {
        var base_id = '#' + this.id.slice(0, -1),
            d = new Date();

        $(base_id + 'y').val(d.getFullYear());
        $(base_id + 'm').val(d.getMonth() + 1);
        $(base_id + 'd').val(d.getDate());
        fuzzyEnable(this);
    });

}
