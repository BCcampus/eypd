(function ($) {
    $(document).ready(function () {
        $('#past').click(function () {
            $(this).html() === 'Collapse past events <i class="glyphicon glyphicon-triangle-bottom"></i>' ? $(this).html('Expand to see all past events <i class="glyphicon glyphicon-triangle-right"></i>') : $(this).html('Collapse past events <i class="glyphicon glyphicon-triangle-bottom"></i>');
        });
    });
})(jQuery);