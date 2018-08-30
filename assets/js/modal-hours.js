jQuery(document).ready(function ($) {

    $eventdate = '#event-date';   // input field where date picker will show up
    $($eventdate).datepicker('hide');
    $($eventdate).on('click', function(){

        $($eventdate).datepicker({
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true
        });
        $($eventdate).datepicker('show');
    });

    $('#hours-modal').on('click', '.btn-save', function(){

        // send the data back to php, so we can use it to create the new user meta, and re-calculate the hours

        location.reload(true); // reload the page to recalculate various items in the page, including the donut and past events table.

        // if all is good, close the modal
        $('#hours-modal').modal('hide');// close the modal
    });
});