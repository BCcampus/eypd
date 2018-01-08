/**
 * Stop playing the video when modal is closed
 */

(function ($) {
    $(document).ready(function () {
        jQuery('#video').on('hidden.bs.modal', function (e) {
            jQuery('#video iframe').attr("src", jQuery("#video  iframe").attr("src"));
        });
    });
})(jQuery);