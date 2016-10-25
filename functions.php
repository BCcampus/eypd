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
|
|
*/
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 100, 100 );

/**
 * Load up our scripts
 *
 */
function eypd_load_scripts() {
	$template_dir = get_stylesheet_directory_uri();
	wp_enqueue_script( 'google-maps-api', 'https://maps.google.com/maps/api/js?key=AIzaSyBZkJ6T__mkEkwdr1SIK-dHfyjbKJqBy70', array( 'jquery' ), '1.0', false );
	wp_enqueue_script( 'gmap3', $template_dir . '/assets/js/gmap3.min.js', array( 'jquery' ), '1.0', false );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'custom_script', $template_dir . '/assets/js/custom.js', array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'tinyscrollbar', $template_dir . '/assets/js/jquery.tinyscrollbar.min.js', array( 'jquery' ), '1.0', true );
}

add_action( 'wp_enqueue_scripts', 'eypd_load_scripts' );


/**
 * Queries database for all posts that have post_meta
 * with a key = '_location_id'
 *
 * @return array
 */
function eypd_get_unique_location_id($sets = array('loc_post_id')) {
	$loc_id      = array();
	$loc_post_id = array();
	$event_post_id = array();

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
		$event_post_id[] = get_the_ID();
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

	if (in_array('loc_post_id', $sets)) {
		$output['loc_post_id'] = $loc_post_id;
	}
	if (in_array('loc_id', $sets)) {
		$output['loc_id'] = $loc_id;
	}
	if (in_array('event_post_id', $sets)) {
		$output['event_post_id'] = $loc_post_id;
	}
	return $output;
}

function eypd_distance($lat1, $lon1, $lat2, $lon2) {
	$theta = $lon1 - $lon2;
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$kms = $dist * 146.2893696; // 60 * 1.1515 * 1.609344;
	$unit = strtoupper($unit);

	return $kms;
}

function eypd_center($et_var_lat, $et_var_lng) {
	$et_center_lat = array_sum($et_var_lat) / count($et_var_lat);
	$et_center_lng = array_sum($et_var_lng) / count($et_var_lng);
	return array($et_center_lat, $et_center_lng);
}

function eypd_event_data($post_id) {
	$output = array();
	if ($post = get_post( $post_id )) {
		
		$output['title'] = $post->post_title;
		$output['excerpt'] = $post->post_excerpt;
		$output['image'] = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
		$output['meta'] = get_post_custom($post_id);
		$output['event'] = em_get_event($post_id, 'event_id');

		return $output;
	}
	return false;
}

function eypd_event_output($post_id = 0, $data = array(), $index = -1) {
	$output = "";
	if ($index == -1) {
		$index = $post_id;
	}

	// get the data
	if ($data === array()) {
		$data = eypd_event_data($post_id);
	}
	// get the design
	$output = sprintf('<div id="et_marker_%d" class="et_marker_info"><div class="location-description"> <div class="location-title"> <h2>%s</h2> <div class="listing-info"></div> </div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->', $index, $data['title']);

	return $output;
}




add_action('wp_ajax_nopriv_et_fetch', 'et_fetch');
add_action('wp_ajax_et_fetch', 'et_fetch');

// use it for two uses -- the Ajax response and th post info
function et_fetch($post_id = -1, $ajax = TRUE) {
	if ($ajax == TRUE) {	
		$output = eypd_event_output($post_id);
		echo json_encode($output); //encode into JSON format and output
		die(); //stop "0" from being output
	}
}

