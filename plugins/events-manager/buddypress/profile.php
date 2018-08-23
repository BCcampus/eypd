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

echo "<div class='row'><p class='desc col-md-8'>This is your professional development activity page - a personal record of your training events, events you plan on 
attending, and the professional development hours you have accumulated.</p></div>";

/*
|--------------------------------------------------------------------------
| Certificate hours
|--------------------------------------------------------------------------
|
|
|
|
*/
if ( bp_is_my_profile() ) { ?>
	<div class="bg-info at-a-glance">
	<div class="certhours">
		<?php
		$user_hours = get_user_meta( $bp->displayed_user->id, 'eypd_cert_hours', true );
		// tally up the hours
		$num    = eypd_cumulative_hours( $user_hours );
		$needed = ( $num > 40 ) ? '0' : 40 - $num;

		echo '<p>You have <a href="#completed">completed</a> <b>';
		echo ( $num ) ? $num : '0';
		echo '/40</b> certification hours.</p>';
		echo '<p>You <a href="#needed">need</a> <b>' . $needed . '</b> certification hours.</p>';
		?>
	</div>


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
		?>
		<form id="eypd_countdown" class="eypd-countdown" action=""
			  method="post">
			<div class="certexpire">
				<label for="expiry-date">Certificate Expiration</label>
				<input id="expiry-date" value="<?php if ( $cert_expires ) {
					echo $cert_expires;
} else { ?>Select date...<?php } ?>" name="expiry-date"/>
				<input class="right" type="submit" value="Save">
				<div id="certcoutdown"><p>calculating...</p></div>
			</div>
		</form>
		</div>
	<?php

	/*
	|--------------------------------------------------------------------------
	| My Posted Events
	|--------------------------------------------------------------------------
	|
	| List of events that an organizer has created
	|
	|
	*/

	if ( user_can( $bp->displayed_user->id, 'edit_events' ) ) {
		?>
		<h2 class="top-padding"><?php _e( 'My posted events', 'events-manager' ); ?></h2>
		<?php
		$args          = [
			'owner'         => $bp->displayed_user->id,
			'format_header' => get_option( 'dbem_bp_events_list_format_header' ),
			'format'        => get_option( 'dbem_bp_events_list_format' ),
			'format_footer' => get_option( 'dbem_bp_events_list_format_footer' ),
			'owner'         => $bp->displayed_user->id,
			'pagination'    => 1,
		];
		$args['limit'] = ! empty( $args['limit'] ) ? $args['limit'] : get_option( 'dbem_events_default_limit' );
		if ( EM_Events::count( $args ) > 0 ) {
			echo EM_Events::output( $args );
		} else {
			?>
			<p><?php _e( 'No Events', 'events-manager' ); ?>.
				<?php if ( get_current_user_id() === $bp->displayed_user->id ) : ?>
					<a href="<?php echo home_url() . '/post-event'; ?>"><?php _e( 'Add Event', 'events-manager' ); ?></a>
				<?php endif; ?>
			</p>
			<?php
		}
	}

	/*
	|--------------------------------------------------------------------------
	| D3 Chart
	|--------------------------------------------------------------------------
	|
	| Provides data for d3 chart
	|
	|
	*/

	$cert_hours       = get_user_meta( $bp->displayed_user->id, 'eypd_cert_hours', true );
	$chart_data_array = eypd_hours_and_categories( $cert_hours );
	$chart_data_json  = eypd_d3_array( $chart_data_array );
	// Pass the data to donut.js
	wp_localize_script( 'donut', 'donut_data', $chart_data_json );
	$no_donut = get_stylesheet_directory_uri() . '/dist/images/donut_placeholder.png';
	echo '<h2 class="top-padding">Event summary</h2>';
	echo ( $chart_data_json ) ? '<div class="donut"></div>' : "<div class='no-donut'><img alt='graphical representation of completed certificate hours' src={$no_donut}></div>";

	/*
	|--------------------------------------------------------------------------
	| Training
	|--------------------------------------------------------------------------
	|
	|
	|
	|
	*/
	echo '<h2 class="top-padding">Training</h2>';
	echo '<p>Adding an event to myEYPD does not confirm your registration, nor does deleting an event cancel your registration. To officially register for a professional development event you must contact the agency responsible for the training event.</p>';
	echo do_shortcode( '[cwp_notify]' );

	$past_ids    = [];
	$future_ids  = [];
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
		$past_count   = count( $past_ids );
		$future_count = count( $future_ids );
	}
	/*
	|--------------------------------------------------------------------------
	| Upcoming Events
	|--------------------------------------------------------------------------
	|
	|
	|
	|
	*/
	?>
	<a name="needed"></a>
	<div id="accordion">
		<div class="card">
			<div class="card-header" id="headingOne">
				<h3 class="future top-padding"><?php _e( 'My Upcoming Events (', 'events-manager' );
					echo $future_count; ?>)
				</h3>
			</div>
			<div class="card-body">

				<?php
				// Future Events Only
				if ( isset( $future_ids ) && count( $future_ids ) > 0 ) { ?>

					<table cellpadding="0" cellspacing="0"
						   class="events-table">
						<thead>
						<tr>
							<th class="event-time" width="150">Date/Time
							</th>
							<th class="event-description" width="*">Upcoming
								Event
							</th>
							<th>Register</th>
							<?php if ( is_user_logged_in() ) {
								echo '<th class="event-delete">Delete this event from my profile</th>';
}
							?>
							<th class="event-ical" width="*">Add to
								Calendar
							</th>
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
								<?php
								$attributes = $EM_Event->event_attributes;
								if ( ! empty( $attributes['Registration Link'] ) ) {
									$maybe_url = eypd_maybe_url( $attributes['Registration Link'] );
								};
								$link = ( $maybe_url ) ? "<a href='{$maybe_url}' target='_blank'>Contact Organizer <i class='glyphicon glyphicon-new-window'></i></a>" : "<a href='{$EM_Event->guid}'>Contact Organizer</a>";
								?>
								<td><?php echo $link; ?></td>
								<?php if ( is_user_logged_in() ) {
									echo '<td>';
									$cancel_link = '';
									if ( ! in_array( $EM_Booking->booking_status, [
											2,
											3,
									] ) && get_option( 'dbem_bookings_user_cancellation' ) && $EM_Event->get_bookings()
																										   ->has_open_time()
									) {
										$cancel_url  = em_add_get_params( $_SERVER['REQUEST_URI'], [
											'action'     => 'booking_cancel',
											'booking_id' => $EM_Booking->booking_id,
											'_wpnonce'   => $nonce,
										] );
										$cancel_link = '<a class="em-bookings-cancel" href="' . $cancel_url . '" onclick="if( !confirm(EM.booking_warning_cancel) ){ return false; }"><i class="glyphicon glyphicon-trash"></i></a>';
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
					$events_url = get_site_url() . '/events';
					echo "<p>See no upcoming events? <a href='{$events_url}'>Browse Events</a></p>";
				}
				?>
			</div>
		</div>
	</div>

	<!-- Past Events, only displayed if there are any -->
	<?php

	// Get count of events user has selected as attended
		$user_hours_meta = get_user_meta( $bp->displayed_user->id, 'eypd_cert_hours', true );
		$attended        = array_count_values( $user_hours_meta );
		( $attended['1'] === null ) ? $attended_count = '0' : $attended_count = $attended['1'];

	if ( $past_count > 0 ) { ?>
			<a name="completed"></a>
			<div id="accordion">
				<div class="card">
					<h3 class="top-padding"><?php _e( 'My Past Events ', 'events-manager' );
					echo '<span class="text-small">(' . $attended_count . '/' . $past_count . ' attended)</span>'; ?>
					</h3>

					<div class="card-header" id="headingTwo">
						<a id="past" class="btn collapsed"
						   data-toggle="collapse"
						   data-target="#collapseTwo"
						   aria-expanded="false" aria-controls="collapseTwo">
							Expand to see all past events<i
									class="glyphicon glyphicon-triangle-right"
									aria-hidden="true"></i>
						</a>
					</div>

					<div id="collapseTwo" class="collapse"
						 aria-labelledby="headingTwo"
						 data-parent="#accordion">
						<div class="card-body">
							<?php
							if ( isset( $past_ids ) && count( $past_ids ) > 0 ) { ?>
								<div class='table-wrap'>
									<form id="eypd_cert_hours"
										  class="eypd-cert-hours"
										  action="" method="post">
										<table id='dbem-bookings-table'
											   class='widefat post fixed'>
											<thead>
											<tr>
												<th class='event-time'
													scope='col'><?php _e( 'Date/Time', 'events-manager' ); ?></th>
												<th class='event-description'
													scope='col'><?php _e( 'Event Description', 'events-manager' ); ?></th>
												<th class='event-hours'
													scope='col'><?php _e( 'Certificate Hours', 'events-manager' ); ?></th>
												<th class='event-attendance'
													scope='col'><?php _e( 'Attended (' . $attended_count . ')', 'events-manager' ); ?></th>
												<th class='event-attendance'
													scope='col'><?php _e( 'Did Not Attend (' . ( $past_count - $attended_count ) . ')', 'events-manager' ); ?></th>
											</tr>
											</thead>
											<tbody>
											<?php
											$nonce = wp_create_nonce( 'eypd_cert_hours' );
											$count = 0;
											// get number of hours in the users profile
											$user_hours  = get_user_meta( $bp->displayed_user->id, 'eypd_cert_hours', true );
											$em_events   = []; // placeholder for events manager events
											$user_events = []; // placeholder for user submitted events
											$past_events = []; // placeholder for merged events manager and user submitted events

											// gets all user submitted events
											$user_submitted = [
												'event_start_date' => 'Apr 13, 2018',
												'event_id '        => 999
											];

											// add user submitted events to $user_events array as an object
											if ( $user_submitted ) {
												$obj = (object)['event_id' => '', 'event_start_date' => ''];
												foreach ( $user_submitted as $key => $value ) {
													$obj->$key = $value;
												}
												$user_events[] = &$obj;
											}

											// add all past events from events manager to $em_events array
											foreach ( $EM_Bookings as $EM_Booking ) {
												// skip over if it's not in the past
												if ( ! in_array( $EM_Booking->event_id, $past_ids ) ) {
													continue;
												}
												$em_events[] = $EM_Booking->get_event();
												$event_id    = $past_ids[ $count ];
											}

											// if user submitted events exist, merge
											if ( $user_events ) {
												$past_events = array_merge( $em_events, $user_events );
											}

											// sort by date
											usort($past_events, function($a, $b) {
												return strtotime($a->event_start_date) - strtotime($b->event_start_date);
											});

											// Loop through all past events from events manager and user submitted
											foreach ( $em_events as $event ) {
											?>
											<tr>
												<td><?php echo $event->output( '#_EVENTDATES<br/>#_EVENTTIMES' ); ?></td>
												<td><?php echo $event->output( '#_EVENTLINK
                {has_location}<br/><i>#_LOCATIONNAME, #_LOCATIONTOWN #_LOCATIONSTATE</i>{/has_location}' ); ?></td>
												<td>
													<?php echo $event->output( '#_ATT{Professional Development Certificate Credit Hours}' ); ?>
												</td>
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
													?>
												</td>
											</tbody>
										</table>
										<input type="hidden" name="_wpnonce"
											   value="<?php echo $nonce; ?>"/>
										<input type="hidden" name="user_id"
											   value="<?php echo $bp->displayed_user->id; ?>"/>
										<input type="hidden" name="action"
											   value="eypd_cert_hours"/>
										<?php
											echo '<input class="right" type="submit" value="Calculate My Hours"/>';
										?>
									</form>
								</div>
								<?php
							} else {
								$events_url = get_site_url() . '/events';
								echo "<p>See no past events? <a href='{$events_url}'>Browse Events</a></p>";
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php } // end past events


	/*
	|--------------------------------------------------------------------------
	| Professional Interests
	|--------------------------------------------------------------------------
	|
	|
	|
	|
	*/
	echo '<h2 class="top-padding">Professional Interests</h2><h5>I\'m interested in learning about:</h5>';
	echo '<div class="professional-interests">';
	echo do_shortcode( '[cwp_notify_em_cat]' );
	$user_id     = get_current_user_id();
	$member_link = bp_core_get_userlink( $user_id, '', true );
	echo '</div>';
	echo "<a href='{$member_link}professional-interests'><input class='right button c-button' type='button' value='Recommend Events'/></a>";

	/*
	|--------------------------------------------------------------------------
	| Suggestions
	|--------------------------------------------------------------------------
	|
	| Send suggestions
	|
	|
	*/
		$options = get_option( 'eypd_settings' );
	if ( isset( $options['contact_form_id'] ) && 0 !== $options['contact_form_id'] ) {
		$id = $options['contact_form_id'];
		echo '<p class="top-padding">Don\'t see what you\'re looking for? Make a suggestion. If there\'s enough interest and workshop facilitators available, we\'ll update our calendar with new events.</p>';
		echo do_shortcode( "[contact-form-7 id='$id']" );
	}
} else {
	echo '<h3>Please <a href=' . wp_login_url() . '>Login</a> or <a href=' . home_url() . '/sign-up>Sign up</a></h3>';
}
