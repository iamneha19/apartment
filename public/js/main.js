jQuery().ready(function() {
    $("#billing-item-flats").select2({
        allowClear: true,
        placeholder: "Search Flats",
        ajax: {
            url: API_URL + "v1/flats?access_token=" + ACCESS_TOKEN + "&society_id=" + society_id,
            dataType: 'json',
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
    });

    $("#billing-item-blocks").select2({
        allowClear: true,
        placeholder: "Search blocks",
        ajax: {
            url: API_URL + "v1/blocks?access_token=" + ACCESS_TOKEN + "&society_id=" + society_id,
            dataType: 'json',
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
    });

    $("#billing-item-buildings").select2({
        allowClear: true,
        placeholder: "Search Buildings",
        ajax: {
            url: API_URL + "v1/buildings?access_token=" + ACCESS_TOKEN + "&society_id=" + society_id,
            dataType: 'json',
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
    });

    $('#eventdate').datetimepicker({
        format: 'YYYY-MM-DD',
        minDate:moment(new Date()).format('YYYY-MM-DD'),
        ignoreReadonly : true,
        widgetPositioning: {
            horizontal: 'left',
            vertical:'bottom'
         }
    });

    // month formatter
    $('#billing-month, #billing-item-start-date, #billing-item-end-date, .month-picker').datetimepicker({
        viewMode: 'months',
        format: 'MMMM YYYY'
    });

    if (typeof alertBox == 'undefined') {
        alertBox = function (status, text, targedForm) {
            if (typeof targedForm == 'undefined') {
                var box = jQuery('.alert.alert-warning');
            } else {
                var box = jQuery(targedForm + ' .alert.alert-warning');
            }

            box.text(text);

            if (status == 'show') {
               box.removeClass('hide');
            } else {
               box.addClass('hide');
            }
        }
    }
});
