<?php
/*
 * Modified from original events manager plugin version: 5.6.6.1 file 'templates/placeholders/event-single.php'
 * Copyright (c) 2016, Marcus Sykes
 * http://wp-events-plugin.com
 * Licence: GPL 2 or later
 *
 * Modifications by Brad Payne
 * Licence GPL 3+
 *
 * This page displays a single event, called during the the_content filter if this is an event page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 *
 * $args - the args passed onto EM_Events::output()
 */
global $EM_Event;
/* @var $EM_Event EM_Event */
echo $EM_Event->output_single();

foreach ( $EM_Event->event_attributes as $key => $att ) {
	if ( 0 === strcmp( 'Registration Link', $key ) ) {
		echo "<p><b>{$key}</b><br><a href='{$att}'>{$att}</a></p>";
	} else {
		echo "<p><b>" . $key . "</b><br>" . $att . "</p>";
	}
};

