<?php
/**
 * This page displays a single event, called during the the_content filter if this is an event page.
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

global $EM_Event;
/* @var $EM_Event EM_Event */
echo $EM_Event->output_single();
echo '<br class="clear">';

foreach ( $EM_Event->event_attributes as $key => $att ) {
	if ( 0 === strcmp( 'Registration Link', $key ) ) {
		echo "<p><b>{$key}</b><br><a href='{$att}'>{$att}</a></p>";
	} else {
		echo '<p><b>' . $key . '</b><br>' . $att . '</p>';
	}
};

