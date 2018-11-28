(function ($) {
    $(document).ready(function () {
        $('#past').click(function () {
            $(this).html() === 'Collapse past events <i class="fa fa-caret-down"></i>' ? $(this).html('Expand to see all past events <i class="fa fa-caret-right"></i>') : $(this).html('Collapse past events <i class="fa fa-caret-down"></i>');
        });
    });
})(jQuery);
