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


/**
 * Load our scripts
 */
function eypd_load_scripts() {
	$template_dir = get_stylesheet_directory_uri();

	// toss Events Manager scripts and their dependancies
	wp_dequeue_script( 'events-manager' );

	// replace script from theme
	// wp_enqueue_script('events-manager', plugins_url('assets/js/events-manager.js',__FILE__), array(), EM_VERSION); 

	$script_deps = array(
		'jquery'                 => 'jquery',
		'jquery-ui-core'         => 'jquery-ui-core',
		'jquery-ui-widget'       => 'jquery-ui-widget',
		'jquery-ui-position'     => 'jquery-ui-position',
		'jquery-ui-sortable'     => 'jquery-ui-sortable',
		'jquery-ui-datepicker'   => 'jquery-ui-datepicker',
		'jquery-ui-autocomplete' => 'jquery-ui-autocomplete',
		'jquery-ui-dialog'       => 'jquery-ui-dialog'
	);

	wp_enqueue_script( 'events-manager', $template_dir . '/assets/js/events-manager.js', array_values( $script_deps ), EM_VERSION );

	/*
	wp_enqueue_script( 'google-maps-api', 'https://maps.google.com/maps/api/js?key=AIzaSyBZkJ6T__mkEkwdr1SIK-dHfyjbKJqBy70', array( 'jquery' ), '1.0', false );
	wp_enqueue_script( 'gmap3', $template_dir . '/assets/js/gmap3.min.js', array( 'jquery' ), '1.0', false );
	wp_enqueue_script( 'jquery-ui-core' );
	*/

	wp_enqueue_script( 'jquery-ui-draggable' );

	wp_enqueue_script( 'custom_script', $template_dir . '/assets/js/custom.js', array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'tinyscrollbar', $template_dir . '/assets/js/jquery.tinyscrollbar.min.js', array( 'jquery' ), '1.0', true );
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

/**
 * Changing state to province on search form
 */
update_option( 'dbem_search_form_state_label', 'Province' );

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
			"Details"       => "Event Description",
			"Category:"     => "Category: (select multiple items [mac]=command,click [pc]=ctrl,click)",
			"Submit %s"     => "Post %s",
		);

		if ( isset( $modify[ $original ] ) ) {
			$translated = $modify[ $original ];
		}
	}

	return $translated;
}

add_filter( 'gettext', 'eypd_terminology_modify', 11, 3 );

/**
 * Queries database for all posts that have post_meta
 * with a key = '_location_id'
 *
 * @return array
 */
function eypd_get_unique_location_id( $sets = array( 'loc_post_id' ) ) {
	$loc_id      = array();
	$loc_post_id = array();

	// set up to get all location longitude, latitude
	$args_ev = array(
		'post_type'     => 'event',
		'post_status'   => 'publish',
		'cache_results' => true,
	);

	$q_ev = new WP_Query( $args_ev );

	// get a unique list of all locations from active events
	while ( $q_ev->have_posts() ) : $q_ev->the_post();
		$loc_id[] = get_post_meta( get_the_ID(), '_location_id', true );
	endwhile;

	rewind_posts();

	// make locations unique
	$loc_id = array_unique( $loc_id );

	// loc_id allows for clustering

	$args_loc = array(
		'post_type'     => 'location',
		'post_status'   => 'publish',
		'cache_results' => true,
	);

	$q_loc = new WP_Query( $args_loc );

	while ( $q_loc->have_posts() ) : $q_loc->the_post();
		$_loc = get_post_meta( get_the_ID(), '_location_id', true );
		if ( in_array( $_loc, $loc_id ) ) {
			$loc_post_id[ $_loc ] = get_the_ID();
		}
	endwhile;

	// loc_post_id allows for individual items

	$output = array();
	if ( in_array( 'loc_post_id', $sets ) ) {
		$output['loc_post_id'] = $loc_post_id;
	}
	if ( in_array( 'loc_id', $sets ) ) {
		$output['loc_id'] = $loc_id;
	}

	return $output;
}

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
 * @param $post_id
 *
 * @return array
 */
function eypd_event_data( $post_id ) {
	return get_post_custom( $post_id );
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
	if ( $data === array() ) {
		$data = eypd_event_data( $post_id );
	}
	// get the design

	// return the design
	return $data;

	return $output;
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