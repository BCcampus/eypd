<?php
/**
 * To ensure compatibility, it is recommended you maintain class, id and form name attributes, unless you now what you're doing.
 * You also must keep the _wpnonce hidden field in this form too.
 *
 * Modified from original events manager plugin version: 5.6.6.1
 * @author Brad Payne
 * @package early-years
 * @since 0.9
 * @license https://www.gnu.org/licenses/gpl.html GPLv3 or later
 *
 * Original:
 * @author Marcus Sykes
 * @copyright Copyright Marcus Sykes
 */

global $EM_Event, $EM_Notices, $bp;

//check that user can access this page
if ( is_object( $EM_Event ) && ! $EM_Event->can_manage( 'edit_events', 'edit_others_events' ) ) {
	?>
	<div class="wrap"><h2><?php esc_html_e( 'Looking to Learn?', 'events-manager' ); ?></h2>
		<p><?php echo __( 'You are using this site in the role as a Learner. Learners may search for, share, and print events. Learners are not authorized to post events.', 'events-manager' ); ?></p>
	</div>
	<?php
	return false;
} elseif ( ! is_object( $EM_Event ) ) {
	$EM_Event = new EM_Event();
}
$required = apply_filters( 'em_required_html', '<i>*</i>' );

echo $EM_Notices;
//Success notice
if ( ! empty( $_REQUEST['success'] ) ) {
	if ( ! get_option( 'dbem_events_form_reshow' ) ) {
		return false;
	}
}

if ( empty( $_REQUEST['success'] ) ) {
	echo "<p class='margin-up'>";
	_e( 'This form is to be completed in order to post events on this web portal. To add your event, fill in the fields below. Fields marked with an * are mandatory. If you have multiple events to post please contact earlyyears[at]bccampus.ca for assistance.', 'early-years' );
	echo '</p>';
}
?>
<form enctype='multipart/form-data' id="event-form" method="post"
	  action="<?php echo esc_url( add_query_arg( array( 'success' => null ) ) ); ?>">
	<div class="wrap">
		<?php do_action( 'em_front_event_form_header' ); ?>
		<?php if ( get_option( 'dbem_events_anonymous_submissions' ) && ! is_user_logged_in() ) : ?>
			<h3 class="event-form-submitter"><?php esc_html_e( 'Your Details', 'events-manager' ); ?></h3>
			<div class="inside event-form-submitter">
				<p>
					<label><?php esc_html_e( 'Name', 'events-manager' ); ?></label>
					<input type="text" name="event_owner_name" id="event-owner-name"
						   value="<?php echo esc_attr( $EM_Event->event_owner_name ); ?>"/>
				</p>
				<p>
					<label><?php esc_html_e( 'Email', 'events-manager' ); ?></label>
					<input type="text" name="event_owner_email" id="event-owner-email"
						   value="<?php echo esc_attr( $EM_Event->event_owner_email ); ?>"/>
				</p>
				<?php do_action( 'em_front_event_form_guest' ); ?>
				<?php do_action( 'em_font_event_form_guest' ); //deprecated ?>
			</div>
		<?php endif; ?>
		<h3 class="event-form-name"><?php esc_html_e( 'Event Name', 'events-manager' ); ?><?php echo $required; ?></h3>
		<div class="inside event-form-name">
			<input type="text" name="event_name" id="event-name"
				   value="<?php echo esc_attr( $EM_Event->event_name, ENT_QUOTES ); ?>"/>
			<br/>
			<i><?php esc_html_e( 'The event name. Example: Birthday party', 'events-manager' ); ?></i>
			<?php em_locate_template( 'forms/event/group.php', true ); ?>
		</div>

		<h3 class="event-form-when"><?php esc_html_e( 'When', 'events-manager' ); ?></h3>
		<div class="inside event-form-when">
			<?php
			if ( empty( $EM_Event->event_id ) && $EM_Event->can_manage( 'edit_recurring_events', 'edit_others_recurring_events' ) && get_option( 'dbem_recurrence_enabled' ) ) {
				em_locate_template( 'forms/event/when-with-recurring.php', true );
			} elseif ( $EM_Event->is_recurring() ) {
				em_locate_template( 'forms/event/recurring-when.php', true );
			} else {
				em_locate_template( 'forms/event/when.php', true );
			}
			?>
		</div>

		<?php if ( get_option( 'dbem_locations_enabled' ) ) : ?>
			<h3 class="event-form-where"><?php esc_html_e( 'Where', 'events-manager' ); ?></h3>
			<p class="margin-up"><i>If your event is online fill in the location fields based on the location of the
					organization sponsoring this event.</i></p>
			<div class="inside event-form-where">
				<?php em_locate_template( 'forms/event/location.php', true ); ?>
			</div>
		<?php endif; ?>

		<h3 class="event-form-details"><?php esc_html_e( 'Details', 'events-manager' ); ?><?php echo $required; ?></h3>
		<p class="margin-up"><i>(What will participants learn?)</i></p>
		<div class="inside event-form-details">
			<div class="event-editor">
				<?php if ( get_option( 'dbem_events_form_editor' ) && function_exists( 'wp_editor' ) ) : ?>
					<?php wp_editor( $EM_Event->post_content, 'em-editor-content', array( 'textarea_name' => 'content' ) ); ?>
				<?php else : ?>
					<textarea name="content" rows="10"
							  style="width:100%"><?php echo $EM_Event->post_content ?></textarea>
					<br/>
					<?php esc_html_e( 'Details about the event.', 'events-manager' ) ?><?php esc_html_e( 'HTML allowed.', 'events-manager' ) ?>
				<?php endif; ?>
			</div>
			<div class="event-extra-details">
				<?php if ( get_option( 'dbem_categories_enabled' ) ) {
					em_locate_template( 'forms/event/categories-public.php', true );
} ?>
				<?php if ( get_option( 'dbem_attributes_enabled' ) ) {
					em_locate_template( 'forms/event/attributes-public.php', true );
} ?>
			</div>
		</div>

		<?php if ( $EM_Event->can_manage( 'upload_event_images', 'upload_event_images' ) ) : ?>
			<h3><?php esc_html_e( 'Event Image', 'events-manager' ); ?></h3>
			<div class="inside event-form-image">
				<?php em_locate_template( 'forms/event/featured-image-public.php', true ); ?>
			</div>
		<?php endif; ?>

		<?php if ( get_option( 'dbem_rsvp_enabled' ) && $EM_Event->can_manage( 'manage_bookings', 'manage_others_bookings' ) ) : ?>
			<!-- START Bookings -->
			<div class="inside event-form-bookings">
				<?php em_locate_template( 'forms/event/bookings.php', true ); ?>
			</div>
			<!-- END Bookings -->
		<?php endif; ?>

		<?php do_action( 'em_front_event_form_footer' ); ?>
	</div>
	<p class="submit">
		<?php if ( empty( $EM_Event->event_id ) ) : ?>
			<input type='submit' class='button-primary'
				   value='<?php echo esc_attr( sprintf( __( 'Submit %s', 'events-manager' ), __( 'Event', 'events-manager' ) ) ); ?>'/>
		<?php else : ?>
			<input type='submit' class='button-primary'
				   value='<?php echo esc_attr( sprintf( __( 'Update %s', 'events-manager' ), __( 'Event', 'events-manager' ) ) ); ?>'/>
		<?php endif; ?>
	</p>
	<input type="hidden" name="event_id" value="<?php echo $EM_Event->event_id; ?>"/>
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpnonce_event_save' ); ?>"/>
	<input type="hidden" name="action" value="event_save"/>
	<?php if ( ! empty( $_REQUEST['redirect_to'] ) ) : ?>
		<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $_REQUEST['redirect_to'] ); ?>"/>
	<?php endif; ?>
</form>
