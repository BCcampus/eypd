<!-- Modal for adding new hours -->
<div id="hours-modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" data-dismiss="modal">×</button>
				<h4 class="modal-title">Record an event you've attended</h4>
			</div>
			<div class="modal-body">
				<p>You may have attended a training event prior to our site launch or one that wasn't advertised here.
					You can still keep tracking by manually adding them using the form below.</p>
				<?php echo '<span class="hours-message">' . __( 'Your event has been recorded.', '' ) . '</span>'; ?>
				<?php echo '<span class="hours-message-error">' . __( 'There was an error recording your event.', '' ) . '</span>'; ?>
				<div class="hours-status"></div>
				<form id="hours-form" method="post">
					<!-- Field names should match events manager field names -->
					<div class="form-group">
						<label for="event_name">Event Title</label>
						<input class="event_name" type="text" name="event_name" size="50" value=""/>
						<label for="event_start_date">Date</label>
						<input id="event_start_date" placeholder="Select Date" name="event_start_date"/>
						<label for="event_hours">Hours</label>
						<input class="event_hours" type="text" placeholder="Duration of event" name="event_hours"
						       size="50" value=""/>
						<?php if ( get_option( 'dbem_categories_enabled' ) ) { ?>
							<label for="event_categories">Categories</label>
							<select name="event_categories[]" multiple size="10" id="event_categories">
								<?php $cats = eypd_get_event_categories();
								foreach ( $cats as $cat ) { ?>
									<option value="<?= $cat['name'] ?>"><?= $cat['name'] ?></option>
								<?php } ?>
							</select>
						<?php } ?>
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