(function ($) {
    $(document).ready(function () {
        $('#future').click(function () {
            $(this).find('i').toggleClass('glyphicon-triangle-right').toggleClass('glyphicon-triangle-bottom');
        });
        $('#past').click(function () {
            $(this).html() === 'Collapse past events <i class="glyphicon glyphicon-triangle-bottom"></i>' ? $(this).html('Expand to see all past events <i class="glyphicon glyphicon-triangle-right"></i>') : $(this).html('Collapse past events <i class="glyphicon glyphicon-triangle-bottom"></i>');
        });
    });
})(jQuery);