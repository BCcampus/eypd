<?php
/*
|--------------------------------------------------------------------------
| Load Composer Dependencies
|--------------------------------------------------------------------------
|
|
|
|
*/
$composer = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer ) ) {
	include_once $composer;
}

/*
|--------------------------------------------------------------------------
| Asynchronous loading js
|--------------------------------------------------------------------------
|
| to improve speed of page load
|
|
*/
add_filter( /**
	* @param $tag
	* @param $handle
	* @param $src
	*
	* @return string
	*/
	'script_loader_tag', function ( $tag, $handle, $src ) {
		$defer = [
			'jquery-migrate',
			'jquery-ui-position',
			'jquery-ui-draggable',
			'jquery-ui-resizable',
			'jquery-ui-mouse',
			'jquery-ui-menu',
			'jquery-ui-sortable',
			'jquery-ui-datepicker',
			'jquery-ui-autocomplete',
			'jquery-ui-dialog',
			'jquery-ui-button',
			'bp-confirm',
			'bp-jquery-query',
			'events-manager',
			'jquery-mobilemenu',
			'jquery-fitvids',
			'modal-video',
			'bootstrap-accordion',
			'd3',
			'donut',
		];

		$async = [
			'bp-jquery-cookie',
			'dtheme-ajax-js',
			'wp-a11y',
			'bp-widget-members',
			'groups_widget_groups_list-js',
			'joyride',
		];

		if ( in_array( $handle, $defer ) ) {
			return "<script defer type='text/javascript' src='{$src}'></script>" . "\n";
		}

		if ( in_array( $handle, $async ) ) {
			return "<script async type='text/javascript' src='{$src}'></script>" . "\n";
		}

		return $tag;
	}, 10, 3
);

/*
|--------------------------------------------------------------------------
| Scripts and Styles
|--------------------------------------------------------------------------
|
| early years look, feel, functionality
|
|
*/

/**
 * need our stylesheet to fire later than the rest
 * in order for: now your base are belong to us
 * infinity theme behaves differently than you would expect parent themes to act
 */
add_action(
	'wp_enqueue_scripts', function () {
		wp_enqueue_style( 'early-years', get_stylesheet_directory_uri() . '/dist/styles/main.css', [ '@:dynamic' ], '', 'screen' );
	}, 11
);

/**
 * back end, front end parity
 */
add_editor_style( get_stylesheet_directory_uri() . '/dist/styles/main.css' );

/**
 * Load our scripts
 */
add_action(
	'wp_enqueue_scripts', function () {
		$template_dir = get_stylesheet_directory_uri();

		// toss Events Manager scripts and their dependencies
		wp_dequeue_script( 'events-manager' );
		remove_action( 'close_body', 'cbox_theme_flex_slider_script' );

		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'markerclusterer', $template_dir . '/dist/scripts/markerclusterer.js', [], false, true );

		$script_deps = [
			'jquery'                 => 'jquery',
			'jquery-ui-core'         => 'jquery-ui-core',
			'jquery-ui-widget'       => 'jquery-ui-widget',
			'jquery-ui-position'     => 'jquery-ui-position',
			'jquery-ui-sortable'     => 'jquery-ui-sortable',
			'jquery-ui-datepicker'   => 'jquery-ui-datepicker',
			'jquery-ui-autocomplete' => 'jquery-ui-autocomplete',
			'jquery-ui-dialog'       => 'jquery-ui-dialog',
			'markerclusterer'        => 'markerclusterer',
		];
		wp_enqueue_script( 'events-manager', $template_dir . '/dist/scripts/events-manager.js', array_values( $script_deps ), isset( $EM_VERSION ) );
		wp_enqueue_script( 'tinyscrollbar', $template_dir . '/dist/scripts/jquery.tinyscrollbar.min.js', [ 'jquery' ], '1.0', true );

		// load popover only for users who aren't logged in
		if ( ! is_user_logged_in() ) {
			wp_enqueue_script( 'bootstrap-tooltip', $template_dir . '/dist/scripts/tooltip.js', [], null, true );
			wp_enqueue_script( 'bootstrap-popover', $template_dir . '/dist/scripts/popover.js', [ 'bootstrap-tooltip' ], null, true );
			wp_enqueue_script( 'initpopover', $template_dir . '/dist/scripts/initpopover.js', [ 'bootstrap-popover' ], null, true );
			wp_enqueue_script( 'popover-dismiss', $template_dir . '/dist/scripts/popover-dismiss.js', [ 'initpopover' ], null, true );
		}

		wp_enqueue_script( 'bootstrap-script', $template_dir . '/dist/scripts/bootstrap.min.js', [], null, true );
		wp_enqueue_style( 'bootstrap-style', $template_dir . '/dist/styles/bootstrap.min.css' );
		wp_enqueue_script( 'modal-video', $template_dir . '/dist/scripts/modal-video.js', [ 'jquery' ], null, true );

		// load styling for datepicker in myEYPD profile page only
		if ( function_exists( 'bp_is_my_profile' ) ) {
			if ( bp_is_my_profile() ) {
				wp_enqueue_style( 'jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
				wp_enqueue_script( 'bootstrap-accordion', $template_dir . '/dist/scripts/accordion.js', [ 'jquery' ], null, true );
				wp_enqueue_script( 'd3', $template_dir . '/dist/scripts/d3.min.js', [], null, true );
				wp_enqueue_script( 'donut', $template_dir . '/dist/scripts/donut.js', [ 'd3' ], null, true );
			}
		}

		if ( is_front_page() ) {
			wp_enqueue_script( 'jquery-tabs', $template_dir . '/dist/scripts/tabs.js', [ 'jquery' ], null, true );
			wp_enqueue_script( 'jquery-ui-tabs' );
		}

		if ( is_singular( 'event' ) ) {
			wp_enqueue_style( 'banner', $template_dir . '/dist/styles/event.css' );
		}

		if ( is_page( 'edit-events' ) || is_page( 'post-event' ) ) {
			wp_enqueue_style( 'media-manager', $template_dir . '/dist/styles/media.css' );
		}
	}, 10
);

/*
|--------------------------------------------------------------------------
| Admin Styles
|--------------------------------------------------------------------------
|
| for admin pages only
|
|
*/

add_action(
	'admin_enqueue_scripts', function () {
		wp_enqueue_style( 'eypd_admin_css', get_stylesheet_directory_uri() . '/dist/styles/admin.css', false, false, 'screen' );
	}
);

// remove from parent theme
remove_action( 'wp_head', 'infinity_custom_favicon' );

/*
|--------------------------------------------------------------------------
| Common
|--------------------------------------------------------------------------
|
|
*/
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 100, 100 );

/*
|--------------------------------------------------------------------------
| Maps
|--------------------------------------------------------------------------
|
| Hijacks files from events-manager plugin
|
|
*/
if ( function_exists( 'em_content' ) ) {
	remove_filter( 'the_content', 'em_content' );
}

if ( function_exists( 'em_content' ) ) {
	remove_filter( 'init', 'em_init_actions' );
}

$eypd_actions = get_stylesheet_directory() . '/eypd-actions.php';
$eypd_events  = get_stylesheet_directory() . '/eypd-events.php';
if ( file_exists( $eypd_actions ) ) {
	require $eypd_actions;
}
if ( file_exists( $eypd_events ) ) {
	require $eypd_events;
}

/*
|--------------------------------------------------------------------------
| Events Manager
|--------------------------------------------------------------------------
|
| Creates a new scope for the events manager short code, and then registers it with events manager.
| It will only lists events with a date greater than today's.
|
|
*/

/**
 *
 * @param $conditions
 * @param $args
 *
 * @return mixed
 */
function eypd_em_scope_conditions( $conditions, $args ) {
	if ( ! empty( $args['scope'] ) && $args['scope'] == 'after-today' ) {
		$current_date        = date( 'Y-m-d', current_time( 'timestamp' ) );
		$conditions['scope'] = " (event_start_date > CAST('$current_date' AS DATE))";
	}

	return $conditions;
}

add_filter( 'em_events_build_sql_conditions', 'eypd_em_scope_conditions', 1, 2 );


/**
 *
 * @param $scopes
 *
 * @return array
 */
function eypd_em_scopes( $scopes ) {
	$my_scopes = [
		'after-today' => 'After Today',
	];

	return $scopes + $my_scopes;
}

add_filter( 'em_get_scopes', 'eypd_em_scopes', 1, 1 );

/*
|--------------------------------------------------------------------------
| Login customization
|--------------------------------------------------------------------------
|
|
|
|
*/

/**
 * Custom stylesheet enqueued at login page
 */
add_action(
	'login_enqueue_scripts', function () {
		wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/dist/styles/login.css' );
	}
);

/**
 * Link logo image to our home_url instead of WordPress.org
 *
 * @return string|void
 */
add_filter(
	'login_headerurl', function () {
		return home_url();
	}
);

/**
 * Give the image our sites name
 *
 * @return string|void
 */
add_filter(
	'login_headertitle', function () {
		return get_bloginfo( 'name' );
	}
);

/**
 * Add custom text to login form
 *
 * @param $message
 *
 * @return string
 */
function eypd_login_message( $message ) {
	if ( empty( $message ) ) {
		$imgdir = get_stylesheet_directory_uri();
		$html   = '<p class="login-logo"><picture><source srcset="' . $imgdir . '/dist/images/eypd-logo-small.webp" type="image/webp"><source srcset="' . $imgdir . '/dist/images/eypd-logo-small.png"><img src="' . $imgdir . '/dist/images/eypd-logo-small.png" width="101" height="92" alt="BC Provincial Government"></picture></p>';
		$html   .= '<p class="logintext">Log in To Your EYPD Account</p>';
		echo $html;
	} else {
		return $message;
	}
}

add_filter( 'login_message', 'eypd_login_message' );

/**
 * Adds Sign Up button and Forgot lost password link
 */
function eypd_login_form() {
	$html = '<p class="signuptext">New to EYPD?</p><p><a class ="button button-primary button-large signup" href="' . home_url() . '/sign-up" title="Sign Up">Sign Up</a></p>';
	$html .= '&nbsp; &#45; &nbsp;<a class ="forgot" href="' . wp_lostpassword_url() . '" title="Lost Password">Forgot Password?</a>';

	echo $html;
}

add_action( 'login_form', 'eypd_login_form' );

/*
|--------------------------------------------------------------------------
| Excerpt
|--------------------------------------------------------------------------
|
| Filter the read more ...
|
|
*/

/**
 * @param $more
 *
 * @return string
 */
function eypd_read_more( $more ) {
	global $post;

	return ' <a href="' . get_the_permalink( $post->ID ) . '">...[Read full article]</a>';
}

add_filter( 'excerpt_more', 'eypd_read_more' );

/*
|--------------------------------------------------------------------------
| Labels/Localization
|--------------------------------------------------------------------------
|
| Addin' sum canadiana to this here 'merican plugin
|
|
*/

function eypd_get_provinces() {
	$provinces = [
		'Alberta',
		'British Columbia',
		'Manitoba',
		'New Brunswick',
		'Newfoundland',
		'Northwest Territories',
		'Nova Scotia',
		'Nunavut',
		'Ontario',
		'Prince Edward Island',
		'Quebec',
		'Saskatchewan',
		'Yukon',
	];

	return $provinces;
}


/**
 * Runs once to set up defaults
 * increase variable $eypd_version to ensure it runs again
 */
function eypd_run_once() {

	// change eypd_version value to run it again
	$eypd_version        = 6.7;
	$current_version     = get_option( 'eypd_version', 0 );
	$img_max_dimension   = 1000;
	$img_min_dimension   = 50;
	$img_max_size        = 8388608;
	$default_no          = [
		'dbem_css_search',
		'dbem_events_form_reshow',
		'dbem_events_anonymous_submissions',
		'dbem_cp_events_comments',
		'dbem_search_form_countries',
		'dbem_locations_page_search_form',
		'dbem_bookings_anonymous',
		'dbem_bookings_approval',
		'dbem_bookings_double',
		'dbem_bookings_login_form',
		'dbem_search_form_geo',
	];
	$default_yes         = [
		'dbem_rsvp_enabled',
		'dbem_recurrence_enabled',
		'dbem_categories_enabled',
		'dbem_attributes_enabled',
		'dbem_cp_events_custom_fields',
		'dbem_locations_enabled',
		'dbem_require_location',
		'dbem_events_form_editor',
		'dbem_cp_events_formats',
		'dbem_gmap_is_active',
		'dbem_cp_events_formats',
		'dbem_bookings_approval_reserved',
		'dbem_bookings_user_cancellation',
		'dbem_bookings_approval_overbooking',
	];
	$default_attributes  = '#_ATT{Target Audience}
#_ATT{Online}{|Yes|No}
#_ATT{Professional Development Certificate}{|Yes|No|Upon Request|Not Currently Available}
#_ATT{Professional Development Certificate Credit Hours}
#_ATT{Registration Fee}
#_ATT{Registration Space}{|Filling Up!|FULL}
#_ATT{Registration Contact Email}
#_ATT{Registration Contact Phone Number}
#_ATT{Registration Link}
#_ATT{Prerequisite(s)}
#_ATT{Required Materials}
#_ATT{Presenter(s)}
#_ATT{Presenter Information}
#_ATT{Event Sponsors}';
	$single_event_format = '<div class="single-event-map">#_LOCATIONMAP</div>
<p>
	<strong>Date/Time</strong><br/>
	Date(s) - #_EVENTDATES<br /><i>#_EVENTTIMES</i>
</p>
<p>
	<strong>Location</strong><br/>
	#_LOCATIONLINK
</p>
<p><strong>Add to My Calendar</strong><br>#_EVENTICALLINK</p>
{has_location}

{/has_location}
<br style="clear:both" />
#_EVENTNOTES
<p>
	<strong>Categories</strong>
	#_CATEGORIES
</p>
{has_bookings}
#_BOOKINGFORM
{/has_bookings}';

	$success_message = '<p><strong>Congratulations! You have successfully submitted your training event.</strong></p> <p><strong>Go to the <a href="' . get_site_url() . '">' . 'homepage</a> and use the search or map feature to find your event.</strong></p>';

	$loc_balloon_format = '<strong>#_LOCATIONNAME</strong><address>#_LOCATIONADDRESS<br>#_LOCATIONTOWN</address>
#_LOCATIONNEXTEVENTS';

	$format_event_list_header = '<table cellpadding="0" cellspacing="0" class="events-table" >
    <thead>
        <tr>
			<th class="event-time" width="150">Date/Time</th>
			<th class="event-description" width="*">Event</th>
			<th class="event-capacity" width="*">Capacity</th>
		</tr>
   	</thead>
    <tbody>';

	$format_event_list = '<tr>
			<td>#_EVENTDATES<br/>#_EVENTTIMES</td>
            <td>#_EVENTLINK
                {has_location}<br/><i>#_LOCATIONNAME, #_LOCATIONTOWN #_LOCATIONSTATE</i>{/has_location}
            </td>
			<td>#_ATT{Registration Space}</td>
        </tr>';

	$format_event_list_footer = '</tbody></table>';

	if ( $current_version < $eypd_version ) {

		update_option( 'dbem_placeholders_custom', $default_attributes );
		update_option( 'dbem_image_max_width', $img_max_dimension );
		update_option( 'dbem_image_max_height', $img_max_dimension );
		update_option( 'dbem_image_min_width', $img_min_dimension );
		update_option( 'dbem_image_min_height', $img_min_dimension );
		update_option( 'dbem_image_max_size', $img_max_size );
		update_option( 'dbem_events_form_result_success', $success_message );
		update_option( 'dbem_events_form_result_success_updated', $success_message );
		update_option( 'dbem_map_text_format', $loc_balloon_format );
		update_option( 'dbem_event_list_item_format', $format_event_list );
		update_option( 'dbem_event_list_item_format_header', $format_event_list_header );
		update_option( 'dbem_event_list_item_format_footer', $format_event_list_footer );
		update_option( 'dbem_single_event_format', $single_event_format );
		update_option( 'dbem_location_event_list_limit', 20 );

		foreach ( $default_no as $no ) {
			update_option( $no, 0 );
		}

		foreach ( $default_yes as $yes ) {
			update_option( $yes, 1 );
		}
		/**
		 * Changes to search for labels
		 */
		update_option( 'dbem_search_form_state_label', 'Province' );
		update_option( 'dbem_search_form_text_label', 'Search by Topic, Keyword or Location' );
		update_option( 'dbem_search_form_dates_label', 'Search by Start Date' );
		update_option( 'dbem_search_form_category_label', 'Search by Category' );
		update_option( 'dbem_search_form_town_label', 'City/Community/Town' );
		update_option( 'dbem_search_form_dates_separator', 'End Date' );

		/**
		 * All events will be in Canada
		 */
		update_option( 'dbem_location_default_country', 'CA' );

		/**
		 * Most events will be in British Columbia
		 */
		update_option( 'eypd_location_default_province', 'British Columbia' );

		/**
		 * Booking submit button text
		 */
		update_option( 'dbem_bookings_submit_button', 'Plan to attend' );

		/**
		 * Booking submit success
		 */
		update_option( 'dbem_booking_feedback', 'Event added! Click on myEYPD (top right of your screen) to find this saved event.' );

		/**
		 * Manage bookings link text
		 */
		update_option( '	dbem_bookings_form_msg_bookings_link', 'My Profile Page' );

		/**
		 * Update option to current version
		 */
		update_option( 'eypd_version', $eypd_version );

	}
}

add_action( 'wp_loaded', 'eypd_run_once' );

/**
 * Changing state to province and other customizations
 *
 * @param $translated
 * @param $original
 * @param $domain
 *
 * @return mixed
 */
function eypd_terminology_modify( $translated, $original, $domain ) {

	if ( 'events-manager' == $domain ) {
		$modify = [
			'State/County:'                                                                  => 'Province:',
			'Details'                                                                        => 'Event Description and Objectives',
			'Category:'                                                                      => 'Category',
			'Submit %s'                                                                      => 'Post %s',
			'You must log in to view and manage your events.'                                => 'You are using this site in the role as a Learner. Learners may search for, share, and print events. Only Organizers may post and edit events.',
			'You are currently viewing your public page, this is what other users will see.' => '',
			'Events'                                                                         => 'myEYPD',
		];
	}

	if ( 'buddypress' == $domain ) {
		$modify = [
			'Register'                                                                                                                  => 'Sign Up',
			'Email Address'                                                                                                             => 'Work Email Address',
			'Registering for this site is easy. Just fill in the fields below, and we\'ll get a new account set up for you in no time.' => 'Fill in the fields below to register as an Organizer or a Learner. <b>Learner</b> — you are primarily looking for training events. <b>Organizer</b> — you are primarily posting training events on behalf of your organization.',
			'You have successfully created your account! Please log in using the username and password you have just created.' => ''
		];
	}

	if ( isset( $modify[ $original ] ) ) {
		$translated = $modify[ $original ];
	}

	return $translated;
}

add_filter( 'gettext', 'eypd_terminology_modify', 11, 3 );

/**
 * Howdy message needs a higher priority and different logic
 * than @see eypd_terminology_modify()
 *
 * @param $translated_text
 * @param $text
 * @param $domain
 *
 * @return mixed
 */
function eypd_howdy_message( $translated_text, $text, $domain ) {
	$new_message = str_replace( 'Howdy,', '', $text );

	return $new_message;
}

add_filter( 'gettext', 'eypd_howdy_message', 10, 3 );

/**
 *
 * @param int   $post_id
 * @param array $data
 *
 * @return array
 */
function eypd_event_output( $post_id = 0, $data = [] ) {
	// get the data
	if ( is_array( $data ) ) {
		$data = get_post_custom( $post_id );
	}

	// return the design
	return $data;
}

/**
 * intercepts output, finds post id#s, uses them to get slugs
 * insert those slugs into the <li> as classes
 *
 * @param string $input
 *
 * @return mixed|string
 */
function eypd_event_etc_output( $input = '' ) {
	$output = $input;
	preg_match_all( '/<li class="category-(\d+)">/', $input, $output_array );
	foreach ( $output_array[1] as $index => $post_id ) {
		$cats       = wp_get_object_terms( $post_id, 'event-categories' );
		$cat_output = $space = '';
		foreach ( $cats as $cat ) {
			$c          = get_category( $cat );
			$cat_output .= $space . 'cat_' . str_replace( '-', '_', $c->slug );
			$space      = ' ';
		}
		$new_classes = "<li class=\"$cat_output\">";
		$output      = str_replace( $output_array[0][ $index ], $new_classes, $output );
	}
	// remove pagination links
	$output = preg_replace( '/<strong><span class=\"page-numbers(.*)<\/span>/i', '', $output );

	return $output;
}


/**
 * use it for two uses -- the Ajax response and the post info
 *
 * @param int  $post_id
 * @param bool $ajax
 */
function et_fetch( $post_id = - 1, $ajax = true ) {
	if ( $ajax == true ) {
		$output = eypd_event_output( $post_id );
		echo json_encode( $output ); //encode into JSON format and output
		die(); //stop "0" from being output
	}
}

add_action( 'wp_ajax_nopriv_cyop_lookup', 'et_fetch' );
add_action( 'wp_ajax_cyop_lookup', 'et_fetch' );

/*
|--------------------------------------------------------------------------
| Navigation
|--------------------------------------------------------------------------
|
| Custom Navigation
|
|
*/

/**
 * remove links/menus from the admin bar,
 * if you are not an admin
 */
function eypd_admin_bar_render() {
	global $wp_admin_bar;

	// check if the admin panel is attempting to be displayed
	if ( ! is_admin() ) {
		$wp_admin_bar->remove_node( 'wp-logo' );
		$wp_admin_bar->remove_node( 'search' );
		$wp_admin_bar->remove_node( 'comments' );
		$wp_admin_bar->remove_node( 'edit' );
		$wp_admin_bar->remove_node( 'edit-profile' );
		$wp_admin_bar->remove_node( 'logout' );
		$wp_admin_bar->remove_node( 'new-content' );
		$wp_admin_bar->remove_node( 'updates' );
		$wp_admin_bar->remove_node( 'my-blogs' );
		$wp_admin_bar->remove_node( 'customize' );
		$wp_admin_bar->remove_node( 'site-name' );
		$wp_admin_bar->remove_node( 'my-account-buddypress' );
		$wp_admin_bar->remove_node( 'bp-notifications' );
		$wp_admin_bar->remove_node( 'itsec_admin_bar_menu' );

		// add my profile link
		$profileurl = eypd_get_my_bookings_url();
		$wp_admin_bar->add_node(
			[
				'id'     => 'my_profile',
				'title'  => 'myEYPD',
				'href'   => $profileurl,
				'parent' => 'user-actions',
				'meta'   => [
					'class' => 'my-profile-page',
				],
			]
		);

		//add logout link after my profile link, and redirect to homepage after logout
		$logouturl = wp_logout_url( home_url() );
		$wp_admin_bar->add_node(
			[
				'id'     => 'logout',
				'title'  => 'Logout',
				'href'   => $logouturl,
				'parent' => 'user-actions',
				'meta'   => [
					'class' => 'my-logout-link',
				],
			]
		);

		// maintain a way for admins to access the dashboard
		if ( current_user_can( 'activate_plugins' ) ) {
			   $url = get_admin_url();
			$wp_admin_bar->add_node(
				[
					'id'    => 'eypd_dashboard',
					'title' => 'Dashboard',
					'href'  => $url,
					'meta'  => [
						'class' => 'my-toolbar-page',
					],
				]
			);
		}
	}
}

add_action( 'wp_before_admin_bar_render', 'eypd_admin_bar_render' );

/**
 * Remove BP sidebar menu items
 */
function eypd_bp_nav() {
	global $bp;
	bp_core_remove_nav_item( 'activity' );
	bp_core_remove_nav_item( 'forums' );
	bp_core_remove_nav_item( 'groups' );
	bp_core_remove_nav_item( 'friends' );
	bp_core_remove_nav_item( 'messages' );
	bp_core_remove_nav_item( 'notifications' );
	//subnav
	bp_core_remove_subnav_item( 'events', 'attending' );
	bp_core_remove_subnav_item( 'events', 'my-bookings' );
	bp_core_remove_subnav_item( 'events', 'my-events' );

}

add_action( 'bp_setup_nav', 'eypd_bp_nav', 1000 );


// Filter wp_nav_menu() to add pop-overs to links in header menu
add_filter(
	'wp_nav_menu_items', function ( $nav, $args ) {
		if ( $args->theme_location == 'main-menu' ) {
			// adds home link to mobile only using bootstraps responsive utilities class
			$nav = '<li class="visible-xs-block home"><a href=' . home_url() . '>Home</a></li>';
			$nav .= '<li class="home"><a href=' . home_url() . '/events>Find Events</a></li>';
			if ( is_user_logged_in() ) {
				$nav .= '<li class="home"><a href=' . home_url() . '/post-event>Post an Event</a></li>';
				$nav .= '<li class="home"><a href=' . home_url() . '/edit-events>Edit Events</a></li>';
				$nav .= '<li class="home"><a href="' . eypd_get_my_bookings_url() . '">' . __( '<i>my</i>EYPD' ) . '</a></li>';
			} else {
				//add popover with a message, and login and sign-up links
				$popover = '<li class="home"><a href="#" data-container="body"  role="button"  data-toggle="popover" data-placement="bottom" data-html="true" data-original-title="" data-content="Please <a href=' . wp_login_url() . '>Login</a> or <a href=' . home_url() . '/sign-up>Sign up</a> to ';
				$nav     .= $popover . 'post events.">Post an Event</a></li>';
				$nav     .= $popover . 'edit your events.">Edit Event</a></li>';
				$nav     .= $popover . ' view your events."><i>my</i>EYPD</a></li>';
			}
		}

		return $nav;
	}, 10, 2
);

/**
 * add Professional Interests to profile area
 * ensure only the member whose page it is can see it
 */
add_action(
	'bp_setup_nav', function () {

		$args = [
			'name'                    => __( 'My Professional Interests', 'early-years' ),
			'slug'                    => 'professional-interests',
			'default_subnav_slug'     => 'prof-int',
			'position'                => 50,
			'show_for_displayed_user' => false,
			'screen_function'         => 'eypd_custom_user_nav_item_screen',
			'item_css_id'             => 'prof-int',
			'site_admin_only'         => false,
		];

		bp_core_new_nav_item( $args );

	}, 11
);


/**
 *
 */
function eypd_custom_user_nav_item_screen() {
	add_action( 'bp_template_content', 'eypd_custom_screen_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * display content on professional interests page
 */
function eypd_custom_screen_content() {

	echo do_shortcode( '[cwp_notify_em_user_cat]' );

}

/*
|--------------------------------------------------------------------------
| Forms
|--------------------------------------------------------------------------
|
|
|
|
*/
/**
 * Validating that required attribute fields are not empty
 */
function eypd_validate_attributes() {
	global $EM_Event;

	// bail early if not an object
	if ( ! is_object( $EM_Event ) ) {
		return false;
	}

	if ( empty( $EM_Event->event_attributes['Professional Development Certificate'] ) ) {
		$EM_Event->add_error( sprintf( __( '%s is required.', 'early-years' ), __( 'Professional Development Certificate', 'early-years' ) ) );
	}

	if ( empty( $EM_Event->event_attributes['Registration Fee'] ) ) {
		$EM_Event->add_error( sprintf( __( '%s is required.', 'early-years' ), __( 'Registration Fee', 'early-years' ) ) );
	}

	return $EM_Event;

}

add_action( 'em_event_validate', 'eypd_validate_attributes' );

/**
 * Add open graph doctype, needed to make FB posts pretty when sharing
 *
 * @param $output
 *
 * @return string
 */

function eypd_doctype_opengraph( $output ) {
	return $output . '
    xmlns:og="http://opengraphprotocol.org/schema/"
    xmlns:fb="http://www.facebook.com/2008/fbml"';
}

add_filter( 'language_attributes', 'eypd_doctype_opengraph' );

/**
 * Add content to the open graph tags for FB
 */
function eypd_fb_opengraph() {
	global $post;
	$img_src = get_stylesheet_directory_uri() . '/dist/images/eypd-logo.png';

	if ( is_single() ) {
		if ( has_post_thumbnail( $post->ID ) ) {
			$img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
		}
		if ( $excerpt = $post->post_excerpt ) {
			$excerpt = strip_tags( $post->post_excerpt );
			$excerpt = str_replace( "", "'", $excerpt );
		} else {
			$excerpt = get_bloginfo( 'description' );
		}
		?>
		<meta property="og:title" content="<?php echo the_title(); ?>"/>
		<meta property="og:description" content="<?php echo $excerpt; ?>"/>
		<meta property="og:type" content="article"/>
		<meta property="og:url" content="<?php echo the_permalink(); ?>"/>
		<meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>"/>
		<meta property="og:image" content="<?php echo $img_src; ?>"/>
		<?php  }
		if ( is_page() ) {
			if ( has_post_thumbnail( $post->ID ) ) {
				$img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
			} ?>
			<meta property="og:title" content="<?php echo the_title(); ?>"/>
			<meta property="og:description" content="<?php echo get_bloginfo( 'description' ); ?>"/>
			<meta property="og:type" content="page"/>
			<meta property="og:url" content="<?php echo get_page_link(); ?>"/>
			<meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>"/>
			<meta property="og:image" content="<?php echo $img_src; ?>"/>
		<?php }
	  else { ?>
		<meta property="og:title" content="<?php echo get_bloginfo( 'name' ); ?>"/>
		<meta property="og:description" content="<?php echo get_bloginfo( 'description' ); ?>"/>
		<meta property="og:type" content="website"/>
		<meta property="og:url" content="<?php echo get_bloginfo( 'url' ); ?>"/>
		<meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>"/>
		<meta property="og:image" content="<?php echo $img_src; ?>"/>
	<?php }
}

add_action( 'wp_head', 'eypd_fb_opengraph', 5 );

/**
 * Makes profile fields descriptions into modals,
 * content of modals are in eypd/templates/*-modal.php
 */
function eypd_profile_field_modals() {

	// check xprofile is activated
	if ( bp_is_active( 'xprofile' ) ) {

		$bp_field_name = bp_get_the_profile_field_name();

		// replace content of $field_description to enable use of modals
		switch ( $bp_field_name ) {

			case 'Agreement Terms:':
				$field_description = '<a href="#terms" data-toggle="modal">Terms and Conditions</a>';

				return $field_description;
			break;

			case 'Position/Role':
				$field_description = '<a href="#role" data-toggle="modal">What’s the difference between Learner and Organizer?</a>';

				return $field_description;
			break;
		}
	}
}

add_filter( 'bp_get_the_profile_field_description', 'eypd_profile_field_modals' );

/**
 * Display a link to FAQ after the submit button on the registration page
 */
function eypd_faq() {
	$html = "<div class='submit faq'><a href=\"https://BCCAMPUS.mycusthelp.ca/webapp/_rs/FindAnswers.aspx?coid=6CFA1D4B2B28F770A1273B\" target=\"_blank\">Need help signing up?</a></div>";
	echo $html;
}

add_filter( 'bp_after_registration_submit_buttons', 'eypd_faq' );

/**
 * Setting a higher default for bookings capacity
 *
 * @return int
 */
function eypd_set_default_spaces() {
	$default = 100;

	return $default;
}

add_filter( 'em_ticket_get_spaces', 'eypd_set_default_spaces' );

/**
 * Adds up hours (if available) from an event attribute
 * hooked into init, integrates with eypd-actions.php
 *
 * @param $ids
 *
 * @return bool|int
 */
function eypd_cumulative_hours( $ids ) {
	if ( ! is_array( $ids ) ) {
		return false;
	}
	$total = 0;
	// input is radio buttons with boolean values
	// true means they attended (default)
	foreach ( $ids as $id => $bool ) {
		if ( false == $bool ) {
			continue;
		}
		$e = em_get_event( $id );
		foreach ( $e->event_attributes as $key => $val ) {
			if ( 0 === strcmp( 'Professional Development Certificate Credit Hours', $key ) ) {
				$total = $total + intval( $val );
			}
		}
	}

	return intval( $total );
}

/**
 * Returns an array of events, with number of hours and categories
 *
 * @param $ids
 *
 * @return array|bool
 */
function eypd_hours_and_categories( $ids ) {
	if ( ! is_array( $ids ) ) {
		return false;
	}
	$cats = $events = [];
	$i    = 0;

	// input is radio buttons with boolean values
	// true means they attended (default)
	foreach ( $ids as $id => $bool ) {
		if ( false == $bool ) {
			continue;
		}
		$e          = em_get_event( $id );
		$categories = wp_get_post_terms( $e->post_id, 'event-categories' );

		if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$cats[] = $category->name;
			}
		}
		foreach ( $e->event_attributes as $key => $val ) {
			if ( 0 === strcmp( 'Professional Development Certificate Credit Hours', $key ) ) {
				$events[ $i ]['hours']      = intval( $val );
				$events[ $i ]['categories'] = $cats;
				$events[ $i ]['name']       = $e->event_name;
				$i ++;
			}
		}
	}

	return $events;
}

/**
 * @param array $data
 *
 * @return mixed|string
 */
function eypd_d3_array( $data ) {
	$cat = $result = [];
	$i   = 0;

	if ( is_array( $data ) ) {
		foreach ( $data as $event ) {

			$unit = ( intval( $event['hours'] ) / count( $event['categories'] ) );

			// events may have more than one category, in which case
			// the total hours need to be shared between them
			foreach ( $event['categories'] as $name ) {
				if ( isset( $cat[ $name ] ) ) {
					$cat[ $name ] = $cat[ $name ] + $unit;
				} else {
					$cat[ $name ] = $unit;
				}
			}
			unset( $unit );

		}

		foreach ( $cat as $k => $v ) {
			$result[ $i ]['label'] = html_entity_decode($k);
			$result[ $i ]['value'] = number_format( $v, 1 );
			$i ++;
		}
	}

	return $result;

}

/**
 * URL to member profile
 */
function eypd_get_my_bookings_url() {
	global $bp;
	if ( ! empty( $bp->events->link ) ) {
		//get member url
		return $bp->events->link;
	} else {
		return '#';
	}
}

/*
|--------------------------------------------------------------------------
| Customize TinyMCE and Media Panel
|--------------------------------------------------------------------------
|
| For the edit-events and post-event pages only
|
*/

/**
 *  Add stylesheet to TinyMCE, allows us to style the content of the editor
 */
add_filter(
	'tiny_mce_before_init', function ( $in ) {
		if ( is_page( 'edit-events' ) or is_page( 'post-event' ) ) {
			$in['content_css'] = get_stylesheet_directory_uri() . '/dist/styles/tinymce.css';

			return $in;
		}

		return $in;
	}
);

/**
 * Force visual editor as default
 */
function eypd_force_default_editor() {
	if ( is_page( 'edit-events' ) or is_page( 'post-event' ) ) {
		return 'tinymce';
	}
}

add_filter( 'wp_default_editor', 'eypd_force_default_editor' );

/**
 * Show only own items in media library panel
 */
function eypd_my_images_only( $query ) {
	if ( $user_id = get_current_user_id() ) {
		// exclude administrator
		if ( ! current_user_can( 'administrator' ) ) {
			$query['author'] = $user_id;
		}
	}

	return $query;
}

add_filter( 'ajax_query_attachments_args', 'eypd_my_images_only' );

/**
 * Rename Add Media button
 */
function eypd_rename_media_button( $translation, $text ) {
	if ( is_page( 'edit-events' ) | is_page( 'post-event' ) && 'Add Media' === $text ) {
		return 'Add Banner Image';
	}

	return $translation;
}

add_filter( 'gettext', 'eypd_rename_media_button', 10, 2 );

/**
 * Rename items in media panel
 */
function eypd_media_view_strings( $strings ) {
	if ( is_page( 'edit-events' ) or is_page( 'post-event' ) ) {
		$strings ['insertMediaTitle'] = 'Add Banner Image (Recommended size: 1000px by 217px )';
		$strings ['insertIntoPost']   = 'Add Banner Image';
	}

	return $strings;
}

add_filter( 'media_view_strings', 'eypd_media_view_strings' );

/**
 * Add a class to image html when inserted into TinyMCE
 */
function eypd_image_tag_class( $class ) {
	$class .= ' banner';

	return $class;
}

add_filter( 'get_image_tag_class', 'eypd_image_tag_class' );

/*
|--------------------------------------------------------------------------
| Banner Image for events
|--------------------------------------------------------------------------
|
| Use the image inserted into post as the banner image
|
|
*/

/**
 * Sanitize and Save only the latest image inserted when creating or editing an event
 */
add_action(
	'content_save_pre', function ( $content ) {
		$maybe_latest_img = '';
		global $post;

		// Only sanitize event post_type
		if ( ! empty( $content ) && 'event' === $post->post_type ) {

			// find all images
			preg_match_all( '/<img[^>]+\>/i', $content, $matches );

			// get one image, maybe the latest
			if ( isset( $matches[0][0] ) ) {
				$maybe_latest_img = $matches [0] [0];
			}

			// remove all images
			$content = preg_replace( '/<img[^>]+\>/i', '', $content );

		}

		return $maybe_latest_img . $content;
	}
);

/**
 * Get the image and display it before the content
 */
add_filter(
	'the_content', function ( $content ) {
		$maybe_latest_img = '';

		// make sure we are on a single event page and that there's content
		if ( ! empty( $content ) && is_singular( 'event' ) ) {
			$maybe_latest_img = '';

			// find all images
			preg_match_all( '/<img[^>]+\>/i', $content, $matches );

			// save one, maybe the latest
			if ( isset( $matches[0][0] ) ) {
				$maybe_latest_img = $matches [0] [0];
			}

			// remove all images
			$content = preg_replace( '/<img[^>]+\>/i', '', $content );
		}

		return $maybe_latest_img . $content;
	}
);

/**
 * Date picker and countdown
 */
function eypd_datepicker_countdown() {

	// only if it's my own profile
	if ( function_exists( 'bp_is_my_profile' ) ) {
		if ( bp_is_my_profile() ) {
			// get the cert expiry date
			global $bp;
			$cert_expires = get_user_meta( $bp->displayed_user->id, 'eypd_cert_expire', true );
			?>
			<!-- jQuery date picker as input for the countdown -->
			<script type="text/javascript">
				jQuery(document).ready(function () {
					$expirydate = '#expiry-date';   // input field where date picker will show up
					jQuery($expirydate).datepicker('hide');
					jQuery($expirydate).click(function () {

						jQuery($expirydate).datepicker({
							dateFormat: 'mm/dd/yy',
							changeMonth: true,
							changeYear: true
						});
						jQuery($expirydate).datepicker('show');
					});
					// end jQuery date picker

					// countdown functionality
					var countDownDate = new Date("<?php echo $cert_expires; ?>").getTime();

					// set interval at 1 second to start countdown and check for changes
					var x = setInterval(function () {

						// today's date and time
						var now = new Date().getTime();

						// distance between now and count down date
						var distance = countDownDate - now;

						// time calculations
						var days = Math.floor(distance / (1000 * 60 * 60 * 24));
						var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
						// var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
						// var seconds = Math.floor((distance % (1000 * 60)) / 1000);

						// expired
						if (distance < 0) {
							clearInterval(x);
							document.getElementById("certcoutdown").innerHTML = "<p class='expired'>Your certificate has expired. Please update your account with a new expiry date.</p>";
						}
						// date in the future
						else if (countDownDate) {
							clearInterval(x);
							document.getElementById("certcoutdown").innerHTML = "<p>Your professional certification expires in <b>" + days + "</b>" + " days and " + "<b>" + hours + "</b>" + " hours " + "</p>";
						}
						// no date
						else {
							clearInterval(x);
							document.getElementById("certcoutdown").innerHTML = "<p class='expired'>Please enter the expiry date of your professional certification.</p>";
						}
					}, 1000);
				});
			</script>
			<?php
		}
	}
}

add_action( 'wp_footer', 'eypd_datepicker_countdown', 10 );


/**
 * Fires when there is an update to the web theme version
 */
function eypd_maybe_update_editor_role() {
	$theme           = wp_get_theme();
	$current_version = $theme->get( 'Version' );
	$last_version    = get_option( 'eypd_theme_version' );
	if ( version_compare( $current_version, $last_version ) > 0 ) {
		eypd_wpcodex_set_capabilities();
	}
}

add_action( 'init', 'eypd_maybe_update_editor_role' );

/**
 * Remove capabilities from editors.
 * will leave the ability to
 * read
 * delete_posts
 * edit_posts
 * upload_files
 * edit_published_pages
 * edit_others_pages
 *
 * Call the function when your plugin/theme is activated.
 */
function eypd_wpcodex_set_capabilities() {

	// Get the role object.
	$editor = get_role( 'editor' );

	// A list of capabilities to remove from editors.
	$caps = [
		'delete_others_pages',
		'delete_others_posts',
		'delete_pages',
		'delete_private_pages',
		'delete_private_posts',
		'delete_published_pages',
		'delete_published_posts',
		'edit_others_posts',
		'edit_published_posts',
		'edit_pages',
		'edit_private_pages',
		'edit_private_posts',
		'manage_categories',
		'manage_links',
		'moderate_comments',
		'publish_pages',
		'publish_posts',
		'read_private_pages',
		'read_private_posts',
		'unfiltered_html',
	];

	foreach ( $caps as $cap ) {

		// Remove the capability.
		$editor->remove_cap( $cap );
	}
}

/**
 * counts and displays number of events
 *
 * @see http://wp-events-plugin.com/documentation/advanced-usage/
 */
function eypd_display_count_events() {

	$results = '';
	$num = '0';

	if ( class_exists( 'EM_Events' ) ) {
		$results = EM_Events::get(
			[
				'scope' => 'future',
				'array' => '',
			]
		);
	}

	if ( is_array( $results ) ) {
		$num = count( $results );
	}

	echo $num;
}

/**
 * Allow users to upload webp
 */
add_filter(
	'upload_mimes', function ( $mime_types ) {
		$mime_types['webp'] = 'image/webp';

		return $mime_types;
	}
);

/*
|--------------------------------------------------------------------------
| PWA
|--------------------------------------------------------------------------
|
| all functions required for pwa
|
|
*/

/**
 * Add favicon, theme color, PWA manifest
 */
add_action(
	'wp_head', function () {
		$manifest = eypd_get_manifest_path();
		echo '<meta name="theme-color" content="#bee7fa"/>' . "\n";
		echo '<link rel="shortcut icon" type="image/x-icon" href="' . get_stylesheet_directory_uri() . '/dist/images/favicon.ico" />' . "\n";
		echo '<link rel="manifest" href="' . $manifest . '">';

	}
);

define( 'EYPD_MANIFEST_ARG', 'manifest_json' );

/**
 *
 */
add_filter(
	'query_vars', function ( $vars ) {
		$vars[] = EYPD_MANIFEST_ARG;

		return $vars;
	}
);

/**
 * @return string
 */
function eypd_get_manifest_path() {
	return add_query_arg( EYPD_MANIFEST_ARG, '1', site_url() );
}

/**
 *
 */
add_action(
	'template_redirect', function () {
		global $wp_query;
		if ( $wp_query->get( EYPD_MANIFEST_ARG ) ) {
			$theme_color = '#bee7fa';
			$lang_dir    = ( is_rtl() ) ? 'rtl' : 'ltr';

			$manifest = [
				'start_url'        => get_bloginfo( 'wpurl' ),
				'short_name'       => 'EYPD',
				'name'             => get_bloginfo( 'name' ),
				'description'      => get_bloginfo( 'description' ),
				'display'          => 'standalone',
				'background_color' => $theme_color,
				'theme_color'      => $theme_color,
				'dir'              => $lang_dir,
				'lang'             => get_bloginfo( 'language' ),
				'orientation'      => 'portrait-primary',
				'icons'            => [
					[
						'src'   => get_stylesheet_directory_uri() . '/dist/images/pwa/eypd-48.png',
						'sizes' => '48x48',
						'type'  => 'image/png',
					],
					[
						'src'   => get_stylesheet_directory_uri() . '/dist/images/pwa/eypd-72.png',
						'sizes' => '72x72',
						'type'  => 'image/png',
					],
					[
						'src'   => get_stylesheet_directory_uri() . '/dist/images/pwa/eypd-96.png',
						'sizes' => '96x96',
						'type'  => 'image/png',
					],
					[
						'src'   => get_stylesheet_directory_uri() . '/dist/images/pwa/eypd-144.png',
						'sizes' => '144x144',
						'type'  => 'image/png',
					],
					[
						'src'   => get_stylesheet_directory_uri() . '/dist/images/pwa/eypd-168.png',
						'sizes' => '168x168',
						'type'  => 'image/png',
					],
					[
						'src'   => get_stylesheet_directory_uri() . '/dist/images/pwa/eypd-192.png',
						'sizes' => '192x192',
						'type'  => 'image/png',
					],
					[
						'src'   => get_stylesheet_directory_uri() . '/dist/images/pwa/eypd-512.png',
						'sizes' => '512x512',
						'type'  => 'image/png',
					],
				],
			];

			wp_send_json( $manifest );
		}
	}, 2
);

/*
|--------------------------------------------------------------------------
| Theme Options Page
|--------------------------------------------------------------------------
|
|
|
|
*/
/**
 * add options page section and fields
 */
add_action(
	'admin_init', function () {
		$page = $options = 'eypd_options';

		register_setting(
			$options,
			'eypd_settings',
			'eypd_sanitize'
		);

		add_settings_section(
			$options . '_section',
			__( 'General Settings', 'early-years' ),
			'',
			$page
		);

		add_settings_field(
			'contact_form_id',
			__( 'Contact form field ID', 'early-years' ),
			'eypd_render_cf7',
			$page,
			$options . '_section'
		);

	}
);

/**
 * render the input field for the form
 */
function eypd_render_cf7() {
	$options = get_option( 'eypd_settings' );

	// add default
	if ( ! isset( $options['contact_form_id'] ) ) {
		$options['contact_form_id'] = '';
	}

	echo "<input type='text' name='eypd_settings[contact_form_id]' value='{$options['contact_form_id']}'><small> A numeric value from a field ID in Contact Form 7</small>";

}

/**
 * sanitize the input field from settings form
 *
 * @param $settings
 *
 * @return mixed
 */
function eypd_sanitize( $settings ) {

	if ( isset( $settings['contact_form_id'] ) ) {
		$settings['contact_form_id'] = absint( $settings['contact_form_id'] );
	}

	return $settings;
}

/**
 * add theme options page
 */
add_action(
	'admin_menu', function () {
		add_submenu_page(
			'themes.php', 'EYPD Options Page', 'EYPD Options', 'manage_options', 'eypd-options', function () {
				echo '<div class="wrap"><form action="options.php" method="post">';
				settings_fields( 'eypd_options' );
				do_settings_sections( 'eypd_options' );
				submit_button();
				echo '</form>';
			}
		);

	}
);

/**
 * Attempts to make a valid url from a string such as: url.ca
 *
 * @param $url
 *
 * @return bool|false|string
 */
function eypd_maybe_url( $url ) {
	if ( is_null( $url ) ) {
		return false;
	}

	$parts = wp_parse_url( $url );

	// tries to ameliorate 'url.ca' as input to '//url.ca'
	if ( ! isset( $parts['scheme'] ) && ! isset( $parts['host'] ) && isset( $parts['path'] ) ) {
		if ( false !== strpos( $parts['path'], '.' ) ) {
			$url = '//' . $parts['path'];
		}
	}

	$valid = wp_http_validate_url( $url );

	return $valid;
}

/**
 * Redirects based on their role after registration when BP activation is skipped.
 * The role value has to come from registration page $_POST, because user is not logged in yet, and
 * field ID's on extended profiles differ on environments and can change
 * @return mixed
 */
function eypd_redirect_after_register() {

	$needles = [ 'Learner', 'Organizer' ];
	// figure out what role they selected at the registration page
	$role_field_id = array_intersect( $needles, $_POST );
	// set the role value
	$role = reset( $role_field_id );
	$html = '';

	// redirect Organizers to edit events
	if ( isset( $_POST['signup_username'] ) && ( $role ) ) {
		if ( $role === 'Organizer' ) {
			$html = '<b>Redirecting ... <meta http-equiv="refresh" content="0; URL=' . home_url() . '/edit-events/" /><a href=' . home_url() . '/edit-events/' . '>click here</a> if you are not automatically redirected.';
		} else { // redirect to the homepage
			$html = '<b>Redirecting ... <meta http-equiv="refresh" content="0; URL=' . home_url() . '/members/' . $_POST["signup_username"] . '/events/"/> <a href="' . home_url() . '/members/' . $_POST["signup_username"] . '/events/"/>click here</a> if you are not automatically redirected.';

		}
		echo $html;
	}
	//
}
add_action( 'bp_after_registration_confirmed', 'eypd_redirect_after_register' );

/**
 * Adds new footer sidebar
 */
function eypd_widgets_init() {
	register_sidebar( [
		'name' => __( 'Footer Last', 'early-years' ),
		'id' => 'sidebar-footer-last',
		'description' => __( 'The last widget in the footer', 'early-years' ),
		'before_widget' => '<article id="%1$s" class="widget %2$s">',
		'after_widget' => '</article>',
		'before_title' => '<h4>',
		'after_title' => '</h4>'
	] );
}
add_action( 'widgets_init', 'eypd_widgets_init' );