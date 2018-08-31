jQuery(document).ready(function ($) {

    var $eventdate = '#event_start_date';   // input field where date picker will show up
    $($eventdate).datepicker('hide');
    $($eventdate).on('click', function () {

        $($eventdate).datepicker({
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true
        });
        $($eventdate).datepicker('show');
    });

    $('#hours-modal').on('click', '.btn-save', function (e) {

        // get all the form values
        var dataArray = $("#hours-form").serializeArray(),
            dataObj = {};
        // let's set the field name as the key
        $(dataArray).each(function(i, field){
            dataObj[field.name] = field.value;
        });

        // Ajax data
        var data = {
            action: 'add_event',
            security: settings.security,
            formdata: dataObj
        };

        // Response
        $.post(settings.ajaxurl, data, function (response) {

            if (response.success === true) {

                // show the success message
                $('.hours-message').slideDown('slow').fadeOut('slow');

            } else {

                // show the error message
                $('.hours-message-error').slideDown('slow').fadeOut('slow');
            }
        });
    });
});