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
function eypd_get_unique_location_id() {
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

	return $loc_post_id;
}

