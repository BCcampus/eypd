jQuery(document).ready(function ($) {
    $('[data-toggle="popover"],[data-original-title]').popover();
    $(document).on('click', function (e) {
        $('[data-toggle="popover"],[data-original-title]').each(function () {
            if (!$(this).is(e.target)) {
                $(this).popover('hide').data('bs.popover').inState.click = false
            }
        });
    });
});
