<?php

$sector_list = get_option( 'eypd_sectors' );

if ( count( $sector_list ) > 0 ) : ?>
	<div class="event-sectors">
		<!-- START Categories -->
		<label for="event_sectors"><?php _e( 'Sector:', 'events-manager' ); ?></label>
		<select name="event_sectors[]" class="event-sectors-select2" id="event_sectors">
			<option selected="selected"></option>
			<?php foreach ( $sector_list as $key => $sector ) {
				$html = '<option value ="' . $sector . '">' . $sector . '</option>';
				echo $html;
} ?>
		</select>
		<!-- END Categories -->
	</div>
<?php endif; ?>
