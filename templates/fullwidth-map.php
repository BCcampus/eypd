<?php
$loc_id = eypd_get_unique_location_id();
?>

<div id="et_main_map"></div>
<script type="text/javascript">
    (function($){
        var $et_main_map = $('#et_main_map');
        et_active_marker = null;
        $et_main_map.gmap3({
            map:{
                options:{
                    <?php
                    // first loop is to center the map on one location (just need one)
                    foreach ( $loc_id as $id ) {
                        $et_location_lat = get_post_meta( $id, '_location_latitude', true );
                        $et_location_lng = get_post_meta( $id, '_location_longitude', true );

                        if ( '' != $et_location_lat && '' != $et_location_lng ) {
                            printf( 'center: [%s, %s],', $et_location_lat, $et_location_lng );
                        }
                        break;
                    }
                    ?>
                    zoom:5,
                    mapTypeId: google.maps.MapTypeId.TERRAIN,
                    mapTypeControl: true,
                    mapTypeControlOptions: {
                        position : google.maps.ControlPosition.LEFT_CENTER,
                        style : google.maps.MapTypeControlStyle.DROPDOWN_MENU
                    },
                    streetViewControlOptions: {
                        position: google.maps.ControlPosition.LEFT_CENTER
                    },
                    navigationControl: false,
                    scrollwheel: false,
                    streetViewControl: true,

                    zoomControl: false
                }
            }
        });
        function et_add_marker(marker_order, marker_lat, marker_lng, marker_description){
            var marker_id = 'et_marker_' + marker_order;
            $et_main_map.gmap3({
                marker : {
                    id : marker_id,
                    latLng : [marker_lat, marker_lng],
                    options: {
                        icon : "<?php echo get_stylesheet_directory_uri(); ?>/images/red-marker.png"
                    },
                    events : {
                        click: function(marker){
                            if (et_active_marker){
                                et_active_marker.setAnimation(null);
                                et_active_marker.setIcon('<?php echo get_stylesheet_directory_uri(); ?>/images/red-marker.png');
                            }
                            et_active_marker = marker;
                            marker.setAnimation(google.maps.Animation.DROP);
                            marker.setIcon('<?php echo get_stylesheet_directory_uri(); ?>/images/blue-marker.png');
                            $(this).gmap3("get").panTo(marker.position);
                            $.fn.et_simple_slider.external_move_to(marker_order);
                        },
                        mouseover: function(marker){
                            $('#' + marker_id).css({ 'display' : 'block', 'opacity' : 0 }).stop(true, true).animate({ bottom : '15px', opacity : 1 }, 500);
                        },
                        mouseout: function(marker){
                            $('#' + marker_id).stop(true, true).animate({ bottom : '50px', opacity : 0 }, 500, function() {
                                $(this).css({ 'display' : 'none' });
                            });
                        }
                    }
                },
                overlay : {
                    latLng : [marker_lat, marker_lng],
                    options : {
                        content : marker_description,
                        offset : {
                            y: - 42,
                            x: - 122
                        }
                    }
                }
            });
        }

        <?php
        // this drops all the pins on the map
        $i = 0;
        foreach ( $loc_id as $id ) {

        $et_location_lat = get_post_meta( $id, '_location_latitude', true );
        $et_location_lng = get_post_meta( $id, '_location_longitude', true );

        if ( '' != $et_location_lat && '' != $et_location_lng ) {
        ?>
        et_add_marker(<?php
            printf( '%1$d, %2$s, %3$s, \'<div id="et_marker_%1$d" class="et_marker_info"><div class="location-description"> <div class="location-title"> <h2>%4$s</h2> <div class="listing-info"></div> </div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->\'', $i, esc_html( $et_location_lat ), esc_html( $et_location_lng ), get_post( $id )->post_title
            );
            ?>);
        <?php
        }
        $i ++;
        }
        ?>
    })(jQuery)
</script>

<div id="et-slider-wrapper" class="et-map-post">
    <div id="et-map-slides">

        <?php
        // the postcard for each of the locations
        $i = 1;
        foreach( $loc_id as $key=>$id ){
            $titletext = get_post( $id )->post_title;
            $thumb = false;
            if ( has_post_thumbnail( $id ) ){
                $et_fullpath = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), array(100,100) );
                $thumb = true;
            }
            ?>
            <div class="et-map-slide<?php if ( 1 == $i ) echo esc_attr( ' et-active-map-slide' ); ?>">
                <div class="thumbnail">
                    <div class="et-description">
                        <h1><a href="<?php echo get_post($id)->guid; ?>"><?php echo $titletext ?></a></h1>
                    </div>
                </div>


                <?php if ( ( $et_location_address = get_post_meta( $id, '_location_address', true ) ) && '' != $et_location_address ) : ?>
                    <div class="et-map-postmeta"><?php echo esc_html( $et_location_address ); ?></div>
                <?php endif; ?>

                <div class="et-place-content">
                    <div class="et-place-text-wrapper">
                        <div class="et-place-main-text">
                            <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
                            <div class="viewport">
                                <div class="overview">
                                    <p>Upcoming Events:</p>
                                    <?php
                                    $l = "[events_list location={$key}]";
                                    echo do_shortcode($l);
                                    ?>
                                </div>
                            </div>
                        </div> <!-- .et-place-main-text -->
                    </div> <!-- .et-place-text-wrapper -->
                </div> <!-- .et-place-content -->
            </div> <!-- .et-map-slide -->
            <?php
            $i ++;
        }
        ?>
    </div> <!-- #et-map-slides -->
</div> <!-- .et-map-post -->


<div id="et-list-view" class="et-normal-listings">
    <h2 id="listing-results"><?php esc_html_e( 'Upcoming Events', 'Explorable' ); ?></h2>

    <div id="et-listings">
        <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
        <div class="viewport">
            <div class="overview">
                <ul>
                    <?php
                    $i = 1;
                    // set up to get all location longitude, latitude
                    $args_ev = array(
                        'post_type' => 'event',
                        'post_status' => 'publish',
                        'cache_results' => true,
                    );

                    $q_ev = new WP_Query( $args_ev );
                    while ( $q_ev->have_posts() ) : $q_ev->the_post();
                        $thumb = false;
                        if ( has_post_thumbnail( get_the_ID() ) ){
                            $et_fullpath = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array(60,60) );
                            $thumb = true;
                        }
                        ?>
                        <li class="<?php if ( 1 == $i ) echo esc_attr( 'et-active-listing ' ); ?>clearfix">
                            <div class="listing-image">
                                <?php if ( $thumb ) { ?>
                                    <img src="<?php// echo $et_fullpath[0] ;?>" />
                                <?php } ?>
                            </div> <!-- .listing-image -->
                            <div class="listing-text">
                                <h3><?php the_title(); ?></h3>
                                <p><?php echo wp_strip_all_tags( get_the_term_list( get_the_ID(), 'event-categories', '', ', ' ) ); ?></p>

                            </div> <!-- .listing-text -->
                            <a href="<?php the_permalink(); ?>" class="et-mobile-link"><?php esc_html_e( 'Read more', 'Explorable' ); ?></a>
                        </li>
                        <?php
                        $i ++;
                    endwhile;
                    ?>
                </ul>
            </div> <!-- .overview -->
        </div> <!-- .viewport -->
    </div> <!-- #et-listings -->
</div> <!-- #et-list-view -->