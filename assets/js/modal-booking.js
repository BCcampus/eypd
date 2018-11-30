/**
 * Close the booking modal when the form is submitted.
 */

(function ($) {
	$(document).ready(function () {
		$('#em-booking-submit').click(function() {
			$('#booking-form').submit();
		});
	});
})(jQuery);


