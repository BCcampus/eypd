(function ($) {
    $(document).ready(function () {
        $('#future,#past').click(function () {
            $(this).find('i').toggleClass('glyphicon-triangle-right').toggleClass('glyphicon-triangle-bottom');
        });
    });
})(jQuery);