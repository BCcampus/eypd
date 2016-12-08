<?php
/*
|--------------------------------------------------------------------------
| Parent theme
|--------------------------------------------------------------------------
|
| enqueue parent and child theme
|
|
*/
function cbox_parent_theme_css() {
	wp_enqueue_style( 'cbox-theme', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'early-years', get_stylesheet_uri(), array( 'cbox-theme' ) );
}

add_action( 'wp_enqueue_scripts', 'cbox_parent_theme_css' );


/*
|--------------------------------------------------------------------------
| Common
|--------------------------------------------------------------------------
|
|
*/
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 100, 100 );

// used in eypd_distance
define( 'CLOSENESS', 5 );

/*
|--------------------------------------------------------------------------
| Maps
|--------------------------------------------------------------------------
|
| Hijacks em-actions.php from events-manager plugin
|
|
*/
include( get_stylesheet_directory() . '/eypd-actions.php' );

/*
|--------------------------------------------------------------------------
| Admin Styles
|--------------------------------------------------------------------------
|
| for admin pages only
|
|
*/
function eypd_admin_style() {
	wp_register_style( 'eypd_admin_css', get_stylesheet_directory_uri() . '/assets/styles/admin.css', false, false, 'screen' );
	wp_enqueue_style( 'eypd_admin_css' );
}

add_action( 'admin_enqueue_scripts', 'eypd_admin_style' );

/**
 * Load our scripts
 */
function eypd_load_scripts() {
	$template_dir = get_stylesheet_directory_uri();

	// toss Events Manager scripts and their dependencies
	wp_dequeue_script( 'events-manager' );


	// replace script from theme
	// wp_enqueue_script('events-manager', plugins_url('assets/js/events-manager.js',__FILE__), array(), EM_VERSION);

	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'markerclusterer', $template_dir . '/assets/js/markerclusterer.js' );


	$script_deps = array(
		'jquery'                 => 'jquery',
		'jquery-ui-core'         => 'jquery-ui-core',
		'jquery-ui-widget'       => 'jquery-ui-widget',
		'jquery-ui-position'     => 'jquery-ui-position',
		'jquery-ui-sortable'     => 'jquery-ui-sortable',
		'jquery-ui-datepicker'   => 'jquery-ui-datepicker',
		'jquery-ui-autocomplete' => 'jquery-ui-autocomplete',
		'jquery-ui-dialog'       => 'jquery-ui-dialog',
		'markerclusterer'       => 'markerclusterer',
	);
	wp_enqueue_script( 'events-manager', $template_dir . '/assets/js/events-manager.js', array_values( $script_deps ), EM_VERSION );
	wp_enqueue_script( 'tinyscrollbar', $template_dir . '/assets/js/jquery.tinyscrollbar.min.js', array( 'jquery' ), '1.0', true );
}

add_action( 'wp_enqueue_scripts', 'eypd_load_scripts', 9 );

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
	$provinces = array(
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
	);

	return $provinces;
}

/**
 * Changing state to province on search form
 */
update_option( 'dbem_search_form_state_label', 'Province' );

/**
 * All events will be in Canada
 */
update_option( 'dbem_location_default_country', 'CA' );

/**
 * Most events will be in British Columbia
 */
update_option( 'eypd_location_default_province', 'British Columbia' );

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
		$modify = array(
			"State/County:" => "Province:",
			"Details"       => "Event Description and Objectives",
			"Category:"     => "Category",
			"Submit %s"     => "Post %s",
		);
	}

	if ( 'buddypress' == $domain ) {
		$modify = array(
			'Register'                                                                                                                  => 'Sign Up',
			'Email Address'                                                                                                             => 'Work Email Address',
			'Registering for this site is easy. Just fill in the fields below, and we\'ll get a new account set up for you in no time.' => 'Fill in the fields below to register as an Organizer or a Learner. Learner — you are primarily looking for training events. Organizer — you are primarily posting training events on this site.',
		);
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
 * @param int $post_id
 * @param array $data
 *
 * @return array
 */
function eypd_event_output( $post_id = 0, $data = array() ) {
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
function eypd_event_etc_output( $input = "" ) {
	$output = $input;
	preg_match_all( "/<li class=\"category-(\d+)\">/", $input, $output_array );
	foreach ( $output_array[1] as $index => $post_id ) {
		$cats       = wp_get_object_terms( $post_id, 'event-categories' );
		$cat_output = $space = "";
		foreach ( $cats as $cat ) {
			$c = get_category( $cat );
			$cat_output .= $space . "cat_" . str_replace( "-", "_", $c->slug );
			$space = " ";
		}
		$new_classes = "<li class=\"$cat_output\">";
		$output      = str_replace( $output_array[0][ $index ], $new_classes, $output );
	}

	return $output;
}


/**
 * use it for two uses -- the Ajax response and th post info
 *
 * @param int $post_id
 * @param bool $ajax
 */
function et_fetch( $post_id = -1, $ajax = true ) {
	if ( $ajax == true ) {
		$output = eypd_event_output( $post_id );
		echo json_encode( $output ); //encode into JSON format and output
		die(); //stop "0" from being output
	}
}

add_action( 'wp_ajax_nopriv_cyop_lookup', 'et_fetch' );
add_action( 'wp_ajax_cyop_lookup', 'et_fetch' );

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
		$wp_admin_bar->remove_node( 'new-content' );
		$wp_admin_bar->remove_node( 'updates' );
		$wp_admin_bar->remove_node( 'my-blogs' );
		$wp_admin_bar->remove_node( 'customize' );
		$wp_admin_bar->remove_node( 'site-name' );

		// maintain a way for admins to access the dashboard
		if ( current_user_can( 'activate_plugins' ) ) {
			$url = get_admin_url();
			$wp_admin_bar->add_node( array(
				'id'    => 'eypd_dashboard',
				'title' => 'Dashboard',
				'href'  => $url,
				'meta'  => array(
					'class' => 'my-toolbar-page'
				)
			) );
		}
	}

}

add_action( 'wp_before_admin_bar_render', 'eypd_admin_bar_render' );
