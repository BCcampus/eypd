<!-- Modal for adding new hours -->
<div id="hours-modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" data-dismiss="modal">×</button>
				<h4 class="modal-title">Record and event you've attended</h4>
			</div>
			<div class="modal-body">
				<p>You may have attended a training event prior to our site launch or one that wasn't advertised here.
					You can still keep tracking by manually adding them using the form below.</p>
				<?php echo '<span class="hours-message">' . __( 'Your event has been recorded.', '' ) . '</span>'; ?>
				<?php echo '<span class="hours-message-error">' . __( 'There was an error recording your event.', '' ) . '</span>'; ?>
				<div class="hours-status"></div>
				<form id="hours-form" method="post">
					<div class="form-group">
						<label for="event-name">Event Title</label>
						<input class="event-name" type="text" name="event-name" size="50" value="" />
						<label for="event-location">Location</label>
						<input class="event-location" type="text" name="event-location" size="50" value="" />
						<label for="event-city">City</label>
						<input class="event-city" type="text" name="event-city" size="50" value="" />
						<label for="event-date">Date</label>
						<input id="event-date" placeholder="Select Date" name="event-date" />
						<?php global $EM_Event;
						$EM_Event = new EM_Event();
						if ( get_option( 'dbem_categories_enabled' ) ) {
							em_locate_template( 'forms/event/categories-public.php', true );
						} ?>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-save" data-dismiss="modal">Save
				</button><?php echo '<span class="hours-loading">' . __( '...', '' ) . '</span>'; ?>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->