<?php
/**
 * @see http://wp-events-plugin.com/documentation/shortcodes/
 *
 * Further configuration, like showing all events at a particular location
 * is available in Events -> Settings -> Formatting -> Maps
 *
 * All Location Related Placeholders can be entered to modify the format and
 * content of text appearing in the balloon describing the location.
 *
 * For instance, entering #_LOCATIONNEXTEVENTS
 * Will show a list of all future events at this location.
 *
 * More documentation on Location Related Placeholders
 * can be found in the admin menu Events -> Settings -> Help
 */

$location = '[locations_map width=100% height=450]';

echo do_shortcode( $location );
