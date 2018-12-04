<?php
/**
 * This file has dependencies in plugins/events-manager/placeholders/bookingform.php
 * Do not include anywhere else.
 * It is here so that all Bootstrap modal functionality is a common location for future updates
 */
?>
<!-- Modal for confirmation form for adding already running events to a users myEYPD event list -->
<a class="btn btn-primary" data-toggle="modal" data-target="#bookingModal" href="#">
	<?php echo esc_attr( get_option( 'dbem_bookings_submit_button' ) ); ?>
</a>
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><?php echo esc_attr( get_option( 'dbem_bookings_submit_button' ) ); ?></h5>
				<a class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</a>
			</div>
			<div class="modal-body">
				<p>This event has happened in the past, are you sure you want to add to your events?</p>
			</div>
			<div class="modal-footer">
				<a class="btn btn-secondary" data-dismiss="modal" href="#" id="booking-modal-close">Close</a>
				<input type="submit" class="em-booking-submit btn btn-primary" id="em-booking-submit" value="Save Event" data-dismiss="modal" />
			</div>
		</div>
	</div>
</div>
