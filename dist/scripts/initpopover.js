/**
 * For performance reasons, the Tooltip and Popover data-apis are opt-in, meaning they must initialized.
 * Let's initialize popovers by selecting them by the data-toggle attribute.
 */

(function ($) {
    $(document).ready(function () {
        $('[data-toggle=popover]').popover();
    });
})(jQuery);