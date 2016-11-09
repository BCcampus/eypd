<?php
/**
 * Returns an array of post ids
 * @see functions.php
 * (
 * [9] => 1488
 * [8] => 1483
 * [7] => 840
 * [6] => 821
 * )
 *
 */

$input			= eypd_get_unique_location_id(array('loc_post_id', 'loc_id', 'event_post_id'));

$event_post_id	= $input['event_post_id'];
$loc_id			= $input['loc_id'];
$loc_post_id	= $input['loc_post_id'];

print "<!-- ".__LINE__."\n";
print "event_post_id \n".print_r($event_post_id, TRUE)."\n";
print "loc_id \n".print_r($loc_id, TRUE)."\n";
print "loc_post_id \n".print_r($loc_post_id, TRUE)."\n";

foreach ($loc_post_id as $id) {
	print print_r(eypd_event_output($id), TRUE);
}

print "-->";

?>
<!--
***************************************
	Required element for map display
****************************************
-->

<div id="et_main_map"></div>

<script type="text/javascript">
	(function ($) {
		var $et_main_map = $('#et_main_map');
		et_active_marker = null;
		$et_main_map.gmap3({
			map: {
				options: {
					<?php
					// first loop is to center the map on one location (just need one)

					$et_var_lat = array();
					$et_var_lng = array();

					foreach ( $loc_post_id as $id ) {
						$et_var_lat[$id] = get_post_meta( $id, '_location_latitude', true );
						$et_var_lng[$id] = get_post_meta( $id, '_location_longitude', true );
					}

					list($et_center_lat, $et_center_lng) = eypd_center($et_var_lat, $et_var_lng);

					if ( '' != $et_center_lat && '' != $et_center_lng ) {
						printf( 'center: [%s, %s],', $et_center_lat, $et_center_lng );
						}	

					$zoom = max(1, (6 - round(( max($et_var_lat) - min($et_var_lat)) / 36)));
					printf( "\n\t\t\t\tzoom: %d,\n", $zoom );

					?>
					mapTypeId: google.maps.MapTypeId.TERRAIN,
					mapTypeControl: true,
					mapTypeControlOptions: {
						position: google.maps.ControlPosition.LEFT_CENTER,
						style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
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

		function et_add_marker(marker_order, marker_lat, marker_lng, marker_description) {
			var marker_id = 'et_marker_' + marker_order;
			$et_main_map.gmap3({
				marker: {
					id: marker_id,
					latLng: [marker_lat, marker_lng],
					options: {
						icon: "<?php echo get_stylesheet_directory_uri(); ?>/assets/images/red-marker.png"
					},
					events: {
						click: function (marker) {
							if (et_active_marker) {
								et_active_marker.setAnimation(null);
								et_active_marker.setIcon('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/red-marker.png');
							}
							et_active_marker = marker;
							marker.setAnimation(google.maps.Animation.DROP);
							marker.setIcon('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/blue-marker.png');
							$(this).gmap3("get").panTo(marker.position);
							//$(this).et_slider_move_to(marker_order);
						},
						mouseover: function (marker) {
							$('#' + marker_id).css({
								'display': 'block',
								'opacity': 0
							}).stop(true, true).animate({bottom: '15px', opacity: 1}, 500);
						},
						mouseout: function (marker) {
							$('#' + marker_id).stop(true, true).animate({bottom: '50px', opacity: 0}, 500, function () {
								$(this).css({'display': 'none'});
							});
						}
					}
				},
				overlay: {
					latLng: [marker_lat, marker_lng],
					options: {
						content: marker_description,
						offset: {
							y: -42,
							x: -122
						}
					}
				}
			});
		}

		<?php

		// this drops all the pins on the map
		$i = 0;
		$closeness = 5; // 5km

		$et_grp_id = array();
		$et_grp_lat = array();
		$et_grp_lng = array();

		foreach ( $loc_post_id as $id ) {
			// get and prep the list

			// fetch higher in the script
			$et_location_lat = $et_var_lat[$id];
			$et_location_lng = $et_var_lng[$id];
			$group = false;
			
			// check for distance and cluster those within "rule" kilometers
			foreach ( $loc_post_id as $this_id ) {
				
				// get and prep the list
				if (($group == false) && ($this_id != $id) && (eypd_distance($et_var_lat[$this_id], $et_var_lng[$this_id], $et_var_lat[$id], $et_var_lng[$id] ) < $closeness)) {
					// group the items
					$group = true;

					// get this one
					$et_grp_id[$this_id][]= $id;
					$et_grp_lat[$this_id] = $et_var_lat[$id];
					$et_grp_lng[$this_id] = $et_var_lng[$id];		
					
					// get the one that was close
					$et_grp_id[$this_id][] = $this_id;
					$et_grp_lat[$this_id] = $et_var_lat[$this_id];
					$et_grp_lng[$this_id] = $et_var_lng[$this_id];		
					
					// print print_r($et_grp_id, TRUE)." $this_id\n";

					$et_grp_id[$this_id] = array_unique($et_grp_id[$this_id]);

					// print print_r($et_grp_id, TRUE)." $this_id\n";
				}
			}

			if ($group == false) {
				// store the marker

				// $marker[$id] = sprintf('et_add_marker(%d, %s, %s, \'<div id="et_marker_%d" class="et_marker_info"><div class="location-description"> <div class="location-title"> <h2>%s</h2> <div class="listing-info"></div> </div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->\');', $i, esc_html( $et_location_lat ), esc_html( $et_location_lng ), $i, get_post( $id )->post_title)."\n";	

				$marker[$id] = sprintf("et_add_marker(%d, %s, %s, '%s');", $i, esc_html( $et_location_lat ), esc_html( $et_location_lng ), eypd_event_output($id, array(), $i))."\n";	

				$i++;
			}
			else {
				// toss the individual markers

				// print __LINE__." about to REMOVE ".$id." and ".$this_id."\n";
				// print print_r($marker, TRUE)."\n";

				unset($marker[$id]);
				unset($marker[$this_id]);
			}
		}
		// output markers

		foreach ($marker as $js) {
			print $js;
		}

		// output groups
		$g = 0;
		foreach ($et_grp_id as $id => $group) {
				$events = $city = "";
				foreach ($group as $single_id) {
					if ($city == "") {
						$city = get_post_meta( $id, '_location_town', true );
					}
					$events .= sprintf("<a href=\"#\" onClick=\"et_fetch(%d)\">%s</a><br/>", $single_id, get_post( $single_id )->post_title);
				}

				printf('et_add_marker(%d, %s, %s, \'<div id="et_marker_%d" class="et_marker_info group"><div class="location-description"> <div class="location-title"> <h2>%s</h2> <div class="listing-info">%s</div> </div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->\');', $i, esc_html( $et_grp_lat[$id] ), esc_html( $et_grp_lng[$id] ), $i, $city, $events)."\n";				
				$i++;
		}

		?>
	})(jQuery)
</script>

<div id="et-slider-wrapper" class="et-map-post">
	<div id="et-map-slides">

		<?php
		// the postcard for each of the locations
		$i = 1;
		foreach ( $loc_id as $key => $id ) {
			$titletext = get_post( $id )->post_title;
			$thumb     = false;
			if ( has_post_thumbnail( $id ) ) {
				$et_fullpath = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), array( 100, 100 ) );
				$thumb       = true;
			}
			?>
			<div class="et-map-slide<?php if ( 1 == $i ) {
				echo esc_attr( ' et-active-map-slide' );
			} ?>">
				<div class="thumbnail">
					<div class="et-description">
						<h1><a href="<?php echo get_post( $id )->guid; ?>"><?php echo $titletext ?></a></h1>
					</div>
				</div>


				<?php if ( ( $et_location_address = get_post_meta( $id, '_location_address', true ) ) && '' != $et_location_address ) : ?>
					<div class="et-map-postmeta"><?php echo esc_html( $et_location_address ); ?></div>
				<?php endif; ?>

				<div class="et-place-content">
					<div class="et-place-text-wrapper">
						<div class="et-place-main-text">
							<div class="scrollbar">
								<div class="track">
									<div class="thumb">
										<div class="end"></div>
									</div>
								</div>
							</div>
							<div class="viewport">
								<div class="overview">
									<p>Upcoming Events:</p>
									<?php
									$l = "[events_list location={$key}]";
									echo do_shortcode( $l );
									?>
								</div>
					<form id="ajaxform" name="slider_search" class="pe_advsearch_form" action="javascript:void(0);" onsubmit="return(new_googlemap_ajaxSearch());">
                	<div class="paf_search"><input class="placeholder" id="search_string" name="search_string" value="" placeholder="Title or Keyword" onclick="this.placeholder=''" onmouseover="this.placeholder='Title or Keyword'" type="text"></div>
               
                              	<div class="paf_row map_post_type" id="toggle_postID" style="display: block; max-height: 445px;">
                    							<div class="mw_cat_title">
							<label><input data-category="eventcategories" onclick="newgooglemap_initialize(this,'');" value="event" checked="checked" class="eventcustom_categories" id="eventcustom_categories" name="posttype[]" type="checkbox"> Event</label><span id="event_toggle" class="toggleon toggle_post_type" onclick="custom_post_type_taxonomy('eventcategories',this)"></span></div>
                        
							 <div class="custom_categories eventcustom_categories" id="eventcategories" style="display: block;">
							<?php 
														 
							$categories = get_categories(array('taxonomy' => 'event-categories'));
							print print_r($categories, TRUE);
														 
							?>

								 
<label for="in-ecategory-41" style="margin-left:0px"><input name="categoryname[]" value="41" id="in-ecategory-41" checked="checked" onclick="newgooglemap_initialize(this,&quot;event&quot;)" type="checkbox"> <img alt="" src="https://earlyyearsbc.ca/wp-content/uploads/2016/09/aboriginal-1.png" width="8" height="14">Aboriginal</label>

<label for="in-ecategory-39" style="margin-left:0px"><input name="categoryname[]" value="39" id="in-ecategory-39" checked="checked" onclick="newgooglemap_initialize(this,&quot;event&quot;)" type="checkbox"> <img alt="" src="https://earlyyearsbc.ca/wp-content/uploads/2016/09/early-care-and-education.png" width="8" height="14">Early Care and Education</label>

<label for="in-ecategory-40" style="margin-left:0px"><input name="categoryname[]" value="40" id="in-ecategory-40" checked="checked" onclick="newgooglemap_initialize(this,&quot;event&quot;)" type="checkbox"> <img alt="" src="https://earlyyearsbc.ca/wp-content/uploads/2016/09/family-support.png" width="8" height="14">Family Support</label>

<label for="in-ecategory-47" style="margin-left:0px"><input name="categoryname[]" value="47" id="in-ecategory-47" checked="checked" onclick="newgooglemap_initialize(this,&quot;event&quot;)" type="checkbox"> <img alt="" src="https://earlyyearsbc.ca/wp-content/uploads/2016/09/health1.png" width="8" height="14">Health</label>


<label for="in-ecategory-48" style="margin-left:0px"><input name="categoryname[]" value="48" id="in-ecategory-48" checked="checked" onclick="newgooglemap_initialize(this,&quot;event&quot;)" type="checkbox"> <img alt="" src="https://earlyyearsbc.ca/wp-content/uploads/2016/09/mental-health.png" width="8" height="14">Mental Health</label>



							 </div>
                         
						                    </div>
                    <div id="toggle_post_type" class="paf_row toggleon" onclick="toggle_post_type();"></div>
                                   </form>

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

<!--****************************************
	Lists events
*****************************************-->
<div id="et-list-view" class="et-normal-listings">
	<h2 id="listing-results"><?php esc_html_e( 'Upcoming Events', 'Explorable' ); ?></h2>

	<div id="et-listings">
		<div class="scrollbar">
			<div class="track">
				<div class="thumb">
					<div class="end"></div>
				</div>
			</div>
		</div>
		<div class="viewport">

<!--****************************************
	Lists events
*****************************************-->
			<div class="overview">
				<ul>
					<?php
					$i = 1;
					// set up to get all location longitude, latitude
					$args_ev = array(
						'post_type'     => 'event',
						'post_status'   => 'publish',
						'cache_results' => true,
					);

					$q_ev = new WP_Query( $args_ev );
					while ( $q_ev->have_posts() ) : $q_ev->the_post();
						$thumb = false;
						if ( has_post_thumbnail( get_the_ID() ) ) {
							$et_fullpath = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array(
								60,
								60
							) );
							$thumb       = true;
						}
						?>
						<li class="<?php if ( 1 == $i ) {
							echo esc_attr( 'et-active-listing ' );
						} ?>clearfix">
							<div class="listing-image">
								<?php if ( $thumb ) { ?>
									<img src="<?php // echo $et_fullpath[0] ;?>"/>
								<?php } ?>
							</div> <!-- .listing-image -->
							<div class="listing-text">
								<h3><?php the_title(); ?></h3>
								<p><?php echo wp_strip_all_tags( get_the_term_list( get_the_ID(), 'event-categories', '', ', ' ) ); ?></p>

							</div> <!-- .listing-text -->
							<a href="<?php the_permalink(); ?>"
							   class="et-mobile-link"><?php esc_html_e( 'Read more', 'Explorable' ); ?></a>
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