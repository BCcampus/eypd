<?php


add_action( 'wp_enqueue_scripts', 'eypd_load_scripts' );
add_action( 'admin_enqueue_scripts', 'eypd_admin_scripts');

// include an updated version of googlemaps
function eypd_load_scripts() {
    $template_dir = get_stylesheet_directory_uri();
    wp_enqueue_script('google-maps-api', 'https://maps.google.com/maps/api/js?sensor=false&key=AIzaSyBZkJ6T__mkEkwdr1SIK-dHfyjbKJqBy70', array('jquery'), '1.0', false);
    wp_enqueue_script( 'gmap3', $template_dir . '/js/gmap3.min.js', array( 'jquery' ), '1.0', false );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-draggable' );
    wp_enqueue_script( 'custom_script', $template_dir . '/js/custom.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'tinyscrollbar', $template_dir . '/js/jquery.tinyscrollbar.min.js', array( 'jquery' ), '1.0', true );
}


// include an updated version of googlemaps
function eypd_admin_scripts( $hook ) {
    // defense
    if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) return;

    // set variables
    global $typenow;
    $template_dir = get_stylesheet_directory_uri();

    if ( ! isset( $typenow ) ) return;

    if ( 'listing' == $typenow ) {
        wp_enqueue_script( 'gmap3-admin', $template_dir . '/js/gmap3.min.js', array( 'jquery' ), '1.0', true );
    }
}

function eypd_is_event_page() {
    if ( is_single() && 'event' == get_post_type() ) return true;
    return ( ( is_home() && is_front_page() ) );
}


//function eypd_home_posts_query( $query = false ) {
//	/* Don't proceed if it's not homepage or the main query */
//	if ( ! ( is_front_page() ) || ! is_a( $query, 'WP_Query' ) || ! $query->is_main_query() ) return;
//
//	$query->set( 'post_type', 'events' );
//	$query->set( 'posts_per_page', '-1' );
//}
//add_action( 'pre_get_posts', 'eypd_home_posts_query', 10 );

function eypd_get_unique_location_id() {
    $loc_id = array();
    $loc_post_id = array();

    // set up to get all location longitude, latitude
    $args_ev = array(
        'post_type' => 'event',
        'post_status' => 'publish',
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
        'post_type' => 'location',
        'post_status' => 'publish',
        'cache_results' => true,
    );

    $q_loc = new WP_Query( $args_loc );

    while ( $q_loc->have_posts() ) : $q_loc->the_post();
        $_loc = get_post_meta( get_the_ID(), '_location_id', true );
        if ( in_array( $_loc, $loc_id ) ) {
            $loc_post_id[$_loc] = get_the_ID();
        }
    endwhile;

    return $loc_post_id;
}

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 100, 100 );

