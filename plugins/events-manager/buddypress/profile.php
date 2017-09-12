<?php
/**
 * Modified from original events manager plugin version: 5.6.6.1
 * events-manager/templates/buddypress/profile.php
 *
 * @author Brad Payne
 * @package early-years
 * @since 0.9.2
 * @license https://www.gnu.org/licenses/gpl.html GPLv3 or later
 *
 * Original:
 * @author Marcus Sykes
 * @copyright Copyright Marcus Sykes
 */

global $bp, $EM_Notices;
echo $EM_Notices;
if ( user_can( $bp->displayed_user->id, 'edit_events' ) ) {
	?>

	<h4><?php _e( 'My Events', 'events-manager' ); ?></h4>
	<?php
	$args          = array(
		'owner'         => $bp->displayed_user->id,
		'format_header' => get_option( 'dbem_bp_events_list_format_header' ),
		'format'        => get_option( 'dbem_bp_events_list_format' ),
		'format_footer' => get_option( 'dbem_bp_events_list_format_footer' ),
		'owner'         => $bp->displayed_user->id,
		'pagination'    => 1,
	);
	$args['limit'] = ! empty( $args['limit'] ) ? $args['limit'] : get_option( 'dbem_events_default_limit' );
	if ( EM_Events::count( $args ) > 0 ) {
		echo EM_Events::output( $args );
	} else {
		?>
		<p><?php _e( 'No Events', 'events-manager' ); ?>.
			<?php if ( get_current_user_id() == $bp->displayed_user->id ) : ?>
				<a href="<?php echo home_url() . '/post-event'; ?>"><?php _e( 'Add Event', 'events-manager' ); ?></a>
			<?php endif; ?>
		</p>
		<?php
	}
}
?>

	<h4><?php _e( "Events I'm Attending", 'events-manager' ); ?></h4>
<?php

$EM_Person   = new EM_Person( $bp->displayed_user->id );
$EM_Bookings = $EM_Person->get_bookings( false, apply_filters( 'em_bp_attending_status', 1 ) );
if ( count( $EM_Bookings->bookings ) > 0 ) {
	$nonce = wp_create_nonce( 'booking_cancel' );

	foreach ( $EM_Bookings as $EM_Booking ) {

		$booking    = $EM_Booking->get_event();
		$event_date = strtotime( $booking->event_start_date, time() );
		$today      = time();

		// separate past and future event_ids
		if ( $today > $event_date ) {
			$past_ids[] = $booking->event_id;
		} elseif ( $today < $event_date ) {
			$future_ids[] = $booking->event_id;
		}
	}
}

// Future Events Only
if ( isset( $future_ids ) && count( $future_ids ) > 0 ) { ?>

	<table cellpadding="0" cellspacing="0" class="events-table">
		<thead>
		<tr>
			<th class="event-time" width="150">Date/Time</th>
			<th class="event-description" width="*">Upcoming Event</th>
			<?php if ( is_user_logged_in() ) {
				echo '<th class="event-delete">Delete this event from my profile</th>';
}
			?>
			<th class="event-ical" width="*">Add to Calendar</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $EM_Bookings as $EM_Booking ) {
			// skip over if it's not in the future
			if ( ! in_array( $EM_Booking->event_id, $future_ids ) ) {
				continue;
			}
			$EM_Event = $EM_Booking->get_event(); ?>
			<tr>
				<td><?php echo $EM_Event->output( '#_EVENTDATES<br/>#_EVENTTIMES' ); ?></td>
				<td><?php echo $EM_Event->output( '#_EVENTLINK
                {has_location}<br/><i>#_LOCATIONNAME, #_LOCATIONTOWN #_LOCATIONSTATE</i>{/has_location}' ); ?></td>

				<?php if ( is_user_logged_in() ) {
					echo '<td>';
					$cancel_link = '';
					if ( ! in_array( $EM_Booking->booking_status, array(
							2,
							3,
					) ) && get_option( 'dbem_bookings_user_cancellation' ) && $EM_Event->get_bookings()->has_open_time()
					) {
						$cancel_url  = em_add_get_params( $_SERVER['REQUEST_URI'], array(
							'action'     => 'booking_cancel',
							'booking_id' => $EM_Booking->booking_id,
							'_wpnonce'   => $nonce,
						) );
						$cancel_link = '<a class="em-bookings-cancel" href="' . $cancel_url . '" onclick="if( !confirm(EM.booking_warning_cancel) ){ return false; }">' . __( 'Delete', 'events-manager' ) . '</a>';
					}
					echo apply_filters( 'em_my_bookings_booking_actions', $cancel_link, $EM_Booking );
					echo '</td>';
}
				?>
				<td><?php echo $EM_Event->output( '#_EVENTICALLINK' ); ?></td>
			</tr>
			<?php
		} ?>
		</tbody>
	</table>
	<?php

} else {
	?>
	<p><?php _e( 'Not attending any events yet.', 'events-manager' ); ?></p>
	<?php
}
?>
	<!-- Past Events Only -->
	<h4><?php _e( "Past Events I've Attended", 'events-manager' ); ?></h4>
<?php
if ( isset( $past_ids ) && count( $past_ids ) > 0 ) { ?>

	<div class='table-wrap'>
		<form id="eypd_cert_hours" class="eypd-cert-hours" action="" method="post">
			<table id='dbem-bookings-table' class='widefat post fixed'>
				<thead>
				<tr>
					<th class='event-time' scope='col'><?php _e( 'Date/Time', 'events-manager' ); ?></th>
					<th class='event-description'
						scope='col'><?php _e( 'Event Description', 'events-manager' ); ?></th>
					<th class='event-hours' scope='col'><?php _e( 'Certificate Hours', 'events-manager' ); ?></th>
					<th class='event-attendance' scope='col'><?php _e( 'Attended', 'events-manager' ); ?></th>
					<th class='event-attendance' scope='col'><?php _e( 'Did Not Attend', 'events-manager' ); ?></th>

				</tr>
				</thead>
				<tbody>
				<?php
				$nonce = wp_create_nonce( 'eypd_cert_hours' );
				$count = 0;
				// save number of hours in the users profile
				$user_hours = get_user_meta( $bp->displayed_user->id, 'eypd_cert_hours', true );

				foreach ( $EM_Bookings

				as $EM_Booking ) {
					// skip over if it's not in the past
					if ( ! in_array( $EM_Booking->event_id, $past_ids ) ) {
						continue;
					}
					$EM_Event = $EM_Booking->get_event();
					$event_id = $past_ids[ $count ]; ?>
				<tr>
					<td><?php echo $EM_Event->output( '#_EVENTDATES<br/>#_EVENTTIMES' ); ?></td>
					<td><?php echo $EM_Event->output( '#_EVENTLINK
                {has_location}<br/><i>#_LOCATIONNAME, #_LOCATIONTOWN #_LOCATIONSTATE</i>{/has_location}' ); ?></td>
					<td>
						<?php echo $EM_Event->output( '#_ATT{Professional Development Certificate Credit Hours}' ); ?>
					</td>
					<?php if ( bp_is_my_profile() ) { ?>
					<td>
						<input id="eypd-cert-hours-<?php echo $event_id; ?>"
							   name=eypd_cert_hours[<?php echo $event_id; ?>]
							   value="1"
							   type='radio' <?php if ( ! isset( $user_hours[ $event_id ] ) ) {
									$user_hours[ $event_id ] = '';
}
						echo ( $user_hours[ $event_id ] || ! isset( $user_hours[ $event_id ] ) ) ? 'checked="checked"' : ''; ?> />
					</td>

					<td>
						<input id="eypd-cert-hours-<?php echo $event_id; ?>"
							   name=eypd_cert_hours[<?php echo $event_id; ?>]
							   value="0"
							   type='radio' <?php if ( ! isset( $user_hours[ $event_id ] ) ) {
									$user_hours[ $event_id ] = '';
}
						echo ( ! $user_hours[ $event_id ] ) ? 'checked="checked"' : ''; ?> />
						<?php
						$count ++;
}
				}
						?>
				</tbody>
			</table>
			<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>"/>
			<input type="hidden" name="user_id" value="<?php echo $bp->displayed_user->id; ?>"/>
			<input type="hidden" name="action" value="eypd_cert_hours"/>

			<!-- calculate hours -->
			<?php if ( bp_is_my_profile() ) { ?>
			<div class="certhours">
				<input class="right" type="submit" value="Calculate My Hours"/>
				<?php
				// tally up the hours
				$num = eypd_cumulative_hours( $user_hours );
				echo '<p>Total Certificate Hours: ';
				echo '<b>';
				echo ( $num ) ? $num : '0';
				echo '</b></p>';
				?>
			</div>
		</form>
		<?php } ?>
	</div>
	<?php
} else {
	_e( 'No past events attended yet.', 'events-manager' );
} ?>

	<!-- countdown to certificate expiry -->
<?php
// save new expiry date
if ( isset( $_POST['expiry-date'] ) ) {
	$newdate = $_POST['expiry-date'];
	// Update/Create User Meta
	update_user_meta( $bp->displayed_user->id, 'eypd_cert_expire', $newdate );
}
//get expiry date
$cert_expires = get_user_meta( $bp->displayed_user->id, 'eypd_cert_expire', true );
if ( bp_is_my_profile() ) {
	?>
	<form id="eypd_countdown" class="eypd-countdown" action="" method="post">
		<div class="certexpire">
			<p>Keep track of when your professional certification expires.</p>
			<input id="expiry-date" value="<?php if ( $cert_expires ) {
				echo $cert_expires;
} else { ?>Select date...<?php } ?>" name="expiry-date"/>
			<input class="right" type="submit" value="Save">
			<div id="certcoutdown"><p>calculating...</p></div>
		</div>
	</form> <?php } ?>
