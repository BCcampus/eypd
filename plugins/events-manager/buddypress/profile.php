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
		'pagination'    => 1
	);
	$args['limit'] = ! empty( $args['limit'] ) ? $args['limit'] : get_option( 'dbem_events_default_limit' );
	if ( EM_Events::count( $args ) > 0 ) {
		echo EM_Events::output( $args );
	} else {
		?>
        <p><?php _e( 'No Events', 'events-manager' ); ?>.
			<?php if ( get_current_user_id() == $bp->displayed_user->id ): ?>
                <a href="<?php echo $bp->events->link . 'my-events/edit/'; ?>"><?php _e( 'Add Event', 'events-manager' ); ?></a>
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
	//Get events here in one query to speed things up
	$event_ids = array();
	foreach ( $EM_Bookings as $EM_Booking ) {
		$event_ids[] = $EM_Booking->event_id;
	}
	echo EM_Events::output( array( 'event' => $event_ids ) );
} else {
	?>
    <p><?php _e( 'Not attending any events yet.', 'events-manager' ); ?></p>
	<?php
}
?>

    <h4><?php _e( "Past Events I've Attended", 'events-manager' ); ?></h4>
<?php

$format_header = '<table cellpadding="0" cellspacing="0" class="events-table" >
    <thead>
        <tr>
			<th class="event-time" width="150">Date/Time</th>
			<th class="event-description" width="*">Past Event</th>
			<th class="event-capacity" width="*">Certificate Hours</th>
		</tr>
   	</thead>
    <tbody>';
$format        = '<tr>
			<td>#_EVENTDATES<br/>#_EVENTTIMES</td>
            <td>#_EVENTLINK
                {has_location}<br/><i>#_LOCATIONNAME, #_LOCATIONTOWN #_LOCATIONSTATE</i>{/has_location}
            </td>
			<td>#_ATT{Professional Development Certificate Credit Hours}</td>
        </tr>';
$format_footer = '</tbody></table>';

if ( count( $EM_Bookings->bookings ) > 0 ) {
	//Get events here in one query to speed things up
	$event_ids = array();
	foreach ( $EM_Bookings as $EM_Booking ) {
		$event_ids[] = $EM_Booking->event_id;
	}
	echo EM_Events::output( array(
		'event'         => $event_ids,
		'scope'         => 'past',
		'format'        => $format,
		'format_header' => $format_header,
		'format_footer' => $format_footer,
	) );
} else {
	?>
    <p><?php _e( 'No past events attended yet.', 'events-manager' ); ?></p>
	<?php
}
