/**
 * Stop playing the video when modal is closed
 */

(function ($) {
    $(document).ready(function () {
        jQuery('#video button.btn, #video button.close').click( function (e) {
            jQuery('#video iframe').attr("src", jQuery("#video  iframe").attr("src"));
        });
    });
})(jQuery);
