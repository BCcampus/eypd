/**
 * Close the booking modal when the form is submitted.
 */

(function ($) {
	$(document).ready(function () {
		console.log("close modal loaded");
		$('#em-booking-submit').click(function(e) {
			$('#bookingModal').modal('hide');
			console.log("close modal");

		});
	});
})(jQuery);


