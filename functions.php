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
//include( get_stylesheet_directory() . '/eypd-actions.php' );

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
}

add_action( 'wp_enqueue_scripts', 'eypd_load_scripts', 9 );

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
 * Calculates distance in kms between two points
 *
 * @param $lat1
 * @param $lon1
 * @param $lat2
 * @param $lon2
 *
 * @return float
 */
function eypd_distance( $lat1, $lon1, $lat2, $lon2 ) {
	$theta = $lon1 - $lon2;
	$dist  = sin( deg2rad( $lat1 ) ) * sin( deg2rad( $lat2 ) ) + cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) * cos( deg2rad( $theta ) );
	$dist  = acos( $dist );
	$dist  = rad2deg( $dist );
	$kms   = $dist * 146.2893696; // 60 * 1.1515 * 1.609344;

	return $kms;
}

/**
 *
 * @param $et_var_lat
 * @param $et_var_lng
 *
 * @return array
 */
function eypd_center( $et_var_lat, $et_var_lng ) {
	$et_center_lat = array_sum( $et_var_lat ) / count( $et_var_lat );
	$et_center_lng = array_sum( $et_var_lng ) / count( $et_var_lng );

	return array( $et_center_lat, $et_center_lng );
}


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
 *
 * @param $json_locations
 * @param $group_key
 *
 * @return array
 */
function eypd_cluster_locations( $json_locations, $group_key ) {
	// iterate through the positions
	foreach ( $json_locations as $location_key => $location_array ) {
		// pull those that are close and group them
		foreach ( $json_locations as $compare_key => $compare_array ) {
			if ( ( $compare_key != $location_key ) && ( eypd_distance( $location_array['location_latitude'], $location_array['location_longitude'], $compare_array['location_latitude'], $compare_array['location_longitude'] ) < CLOSENESS ) ) {
				$group_key ++;
				// pull the location_key, then the compare_key, merge them into one larger balloon and add to the $json_locations_grouped

				$json_locations[ $group_key ]                       = $location_array;
				$json_locations[ $group_key ]['location_name']      = $location_array['location_town'];
				$json_locations[ $group_key ]['location_latitude']  = floatval( ( $location_array['location_latitude'] + $compare_array['location_latitude'] ) / 2 );
				$json_locations[ $group_key ]['location_longitude'] = floatval( ( $location_array['location_longitude'] + $compare_array['location_longitude'] ) / 2 );

				// name
				// slug
				// address
				// town
				// state


				$address = '<span style="display: block;">';
				$address .= ( strlen( $json_locations[ $location_key ]['location_name'] ) > 1 ) ? "<strong>" . $json_locations[ $location_key ]['location_name'] . "</strong><br/>" : "";
				$address .= ( strlen( $json_locations[ $location_key ]['location_address'] ) > 1 ) ? $json_locations[ $location_key ]['location_address'] . "<br/>" : "";
				$address .= ( strlen( $json_locations[ $location_key ]['location_town'] ) > 1 ) ? $json_locations[ $location_key ]['location_town'] : "";
				$address .= ( strlen( $json_locations[ $location_key ]['location_state'] ) > 0 ) ? ", " . $json_locations[ $location_key ]['location_state'] : "";
				$address .= "</span>";

				$json_locations[ $group_key ]['location_balloon'] = str_replace( $address, "", $json_locations[ $group_key ]['location_balloon'] );
				$json_locations[ $group_key ]['location_balloon'] = "<span>" . $json_locations[ $location_key ]['location_balloon'] . "</span>";

				// recycle the variable $address

				$address = '<span style="display: block;">';
				$address .= ( strlen( $json_locations[ $compare_key ]['location_name'] ) > 1 ) ? "<strong>" . $json_locations[ $compare_key ]['location_name'] . "</strong><br/>" : "";
				$address .= ( strlen( $json_locations[ $compare_key ]['location_address'] ) > 1 ) ? $json_locations[ $compare_key ]['location_address'] . "<br/>" : "";
				$address .= ( strlen( $json_locations[ $compare_key ]['location_town'] ) > 1 ) ? $json_locations[ $compare_key ]['location_town'] : "";
				$address .= ( strlen( $json_locations[ $compare_key ]['location_state'] ) > 0 ) ? ", " . $json_locations[ $compare_key ]['location_state'] : "";
				$address .= "</span>";

				$json_locations[ $group_key ]['location_balloon'] = str_replace( $address, "", $json_locations[ $group_key ]['location_balloon'] );
				$json_locations[ $group_key ]['location_balloon'] .= "<span>" . $json_locations[ $compare_key ]['location_balloon'] . "</span>";

				$json_locations[ $group_key ]['location_address'] = "";
				$json_locations[ $group_key ]['location_town']    = "";
				$json_locations[ $group_key ]['location_state']   = "";

				// toss these
				// this should destroy these but not effect that they still exist in the for next loop
				unset( $json_locations[ $location_key ] );
				unset( $json_locations[ $compare_key ] );
			}
		}
	}

	return array( $json_locations, $group_key );
}


/**
 * use it for two uses -- the Ajax response and th post info
 *
 * @param int $post_id
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