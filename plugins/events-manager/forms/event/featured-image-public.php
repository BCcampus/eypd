<?php
/**
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

global $EM_Event;
/* @var $EM_Event EM_Event */
?>
	<p class="margin-up"><i>(The maximum allowed size for images is 8MB. Max dimensions 1000px x 217px)</i></p>
	<p id="event-image-img">
		<?php if ( $EM_Event->get_image_url() != '' ) : ?>
			<img src='<?php echo $EM_Event->get_image_url( 'medium' ); ?>' alt='<?php echo $EM_Event->event_name ?>'/>
		<?php else : ?>
			<?php _e( 'No image uploaded for this event yet', 'events-manager' ) ?>
		<?php endif; ?>
	</p>
	<label for='event_image'><?php _e( 'Upload/change picture', 'events-manager' ) ?></label> <input id='event-image'
																									 name='event_image'
																									 id='event_image'
																									 type='file'
																									 size='40'/>
	<br/>
<?php if ( $EM_Event->get_image_url() != '' ) : ?>
	<label for='event_image_delete'><?php _e( 'Delete Image?', 'events-manager' ) ?></label> <input
			id='event-image-delete' name='event_image_delete' id='event_image_delete' type='checkbox' value='1'/>
<?php endif; ?>
