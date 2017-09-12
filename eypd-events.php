<?php
/**
 * Overrides parent theme function `em_events.php` in order to re-arrange the display
 * of event elements
 *
 * Modified from original events manager plugin version: 5.6.6.1
 * @author Brad Payne
 * @package early-years
 * @since 0.9
 * @license https://www.gnu.org/licenses/gpl.html GPLv3 or later
 *
 * Original:
 * @author Marcus Sykes
 * @copyright Copyright Marcus Sykes
 */

/**
 *
 * @param $page_content
 *
 * @return mixed|void
 */
function eypd_content( $page_content ) {
	global $post, $wpdb, $wp_query, $EM_Event, $EM_Location, $EM_Category;
	if ( empty( $post ) ) {
		return $page_content;
	} //fix for any other plugins calling the_content outside the loop
	$events_page_id         = get_option( 'dbem_events_page' );
	$locations_page_id      = get_option( 'dbem_locations_page' );
	$categories_page_id     = get_option( 'dbem_categories_page' );
	$tags_page_id           = get_option( 'dbem_tags_page' );
	$edit_events_page_id    = get_option( 'dbem_edit_events_page' );
	$edit_locations_page_id = get_option( 'dbem_edit_locations_page' );
	$edit_bookings_page_id  = get_option( 'dbem_edit_bookings_page' );
	$my_bookings_page_id    = get_option( 'dbem_my_bookings_page' );
	//general defaults
	$args         = array(
		'owner'      => false,
		'pagination' => 1,
	);
	$args['ajax'] = isset( $args['ajax'] ) ? $args['ajax'] : ( ! defined( 'EM_AJAX' ) || EM_AJAX );
	if ( ! post_password_required() && in_array( $post->ID, array(
			$events_page_id,
			$locations_page_id,
			$categories_page_id,
			$edit_bookings_page_id,
			$edit_events_page_id,
			$edit_locations_page_id,
			$my_bookings_page_id,
			$tags_page_id,
		) )
	) {
		$content = apply_filters( 'em_content_pre', '', $page_content );
		if ( empty( $content ) ) {
			ob_start();
			if ( $post->ID == $events_page_id && $events_page_id != 0 ) {
				if ( ! empty( $_REQUEST['calendar_day'] ) ) {
					//Events for a specific day
					$args = EM_Events::get_post_search( array_merge( $args, $_REQUEST ) );
					em_locate_template( 'templates/calendar-day.php', true, array( 'args' => $args ) );
				} elseif ( is_object( $EM_Event ) ) {
					em_locate_template( 'templates/event-single.php', true, array( 'args' => $args ) );
				} else {
					// Multiple events page
					$args['orderby'] = get_option( 'dbem_events_default_orderby' );
					$args['order']   = get_option( 'dbem_events_default_order' );
					if ( get_option( 'dbem_display_calendar_in_events_page' ) ) {
						$args['long_events'] = 1;
						em_locate_template( 'templates/events-calendar.php', true, array( 'args' => $args ) );
					} else {
						//Intercept search request, if defined
						if ( ! empty( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'search_events' || $_REQUEST['action'] == 'search_events_grouped' ) ) {
							$args = EM_Events::get_post_search( array_merge( $args, $_REQUEST ) );
						}
						if ( empty( $args['scope'] ) ) {
							$args['scope'] = get_option( 'dbem_events_page_scope' );
						}

						$args['limit'] = ! empty( $args['limit'] ) ? $args['limit'] : get_option( 'dbem_events_default_limit' );
						if ( ! empty( $args['ajax'] ) ) {
							echo '<div class="em-search-ajax">';
						} //AJAX wrapper open
						if ( get_option( 'dbem_event_list_groupby' ) ) {
							em_locate_template( 'templates/events-list-grouped.php', true, array( 'args' => $args ) );
						} else {
							em_locate_template( 'templates/events-list.php', true, array( 'args' => $args ) );
						}
						if ( get_option( 'dbem_events_page_search_form' ) ) {
							//load the search form and pass on custom arguments (from settings page)
							$search_args = em_get_search_form_defaults();
							echo "<div class='c-search'>";
							em_locate_template( 'templates/events-search.php', true, array( 'args' => $search_args ) );
							echo '</div>';
						}
						if ( ! empty( $args['ajax'] ) ) {
							echo '</div>';
						} //AJAX wrapper close
					}
				}
			} elseif ( $post->ID == $locations_page_id && $locations_page_id != 0 ) {
				$args['orderby'] = get_option( 'dbem_locations_default_orderby' );
				$args['order']   = get_option( 'dbem_locations_default_order' );
				$args['limit']   = ! empty( $args['limit'] ) ? $args['limit'] : get_option( 'dbem_locations_default_limit' );
				if ( EM_MS_GLOBAL && is_object( $EM_Location ) ) {
					em_locate_template( 'templates/location-single.php', true, array( 'args' => $args ) );
				} else {
					//Intercept search request, if defined
					if ( ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'search_locations' ) {
						$args = EM_Locations::get_post_search( array_merge( $args, $_REQUEST ) );
					}
					if ( get_option( 'dbem_locations_page_search_form' ) ) {
						//load the search form and pass on custom arguments (from settings page)
						$search_args = em_get_search_form_defaults();
						//remove date and category
						$search_args['search_categories'] = $search_args['search_scope'] = false;
						em_locate_template( 'templates/locations-search.php', true, array( 'args' => $search_args ) );
					}
					if ( ! empty( $args['ajax'] ) ) {
						echo '<div class="em-search-ajax">';
					} //AJAX wrapper open
					em_locate_template( 'templates/locations-list.php', true, array( 'args' => $args ) );
					if ( ! empty( $args['ajax'] ) ) {
						echo '</div>';
					} //AJAX wrapper close
				}
			} elseif ( $post->ID == $categories_page_id && $categories_page_id != 0 ) {
				$args['limit'] = ! empty( $args['limit'] ) ? $args['limit'] : get_option( 'dbem_categories_default_limit' );
				if ( ! empty( $args['ajax'] ) ) {
					echo '<div class="em-search-ajax">';
				} //AJAX wrapper open
				em_locate_template( 'templates/categories-list.php', true, array( 'args' => $args ) );
				if ( ! empty( $args['ajax'] ) ) {
					echo '</div>';
				} //AJAX wrapper close
			} elseif ( $post->ID == $tags_page_id && $tags_page_id != 0 ) {
				$args['limit'] = ! empty( $args['limit'] ) ? $args['limit'] : get_option( 'dbem_tags_default_limit' );
				if ( ! empty( $args['ajax'] ) ) {
					echo '<div class="em-search-ajax">';
				} //AJAX wrapper open
				em_locate_template( 'templates/tags-list.php', true, array( 'args' => $args ) );
				if ( ! empty( $args['ajax'] ) ) {
					echo '</div>';
				} //AJAX wrapper close
			} elseif ( $post->ID == $edit_events_page_id && $edit_events_page_id != 0 ) {
				em_events_admin();
			} elseif ( $post->ID == $edit_locations_page_id && $edit_locations_page_id != 0 ) {
				em_locations_admin();
			} elseif ( $post->ID == $my_bookings_page_id && $my_bookings_page_id != 0 ) {
				em_my_bookings();
			} elseif ( $post->ID == $edit_bookings_page_id && $edit_bookings_page_id != 0 ) {
				em_bookings_admin();
			}
			$content = ob_get_clean();
			//If disable rewrite flag is on, then we need to add a placeholder here
			if ( get_option( 'dbem_disable_title_rewrites' ) == 1 ) {
				$content = str_replace( '#_PAGETITLE', em_content_page_title( '' ), get_option( 'dbem_title_html' ) ) . $content;
			}
			//Now, we either replace CONTENTS or just replace the whole page
			if ( preg_match( '/CONTENTS/', $page_content ) ) {
				$content = str_replace( 'CONTENTS', $content, $page_content );
			}
			if ( get_option( 'dbem_credits' ) ) {
				$content .= '<p style="color:#999; font-size:11px;">Powered by <a href="http://wp-events-plugin.com" style="color:#999;" target="_blank">Events Manager</a></p>';
			}
		}

		return apply_filters( 'em_content', '<div id="em-wrapper">' . $content . '</div>' );
	}

	return $page_content;
}

add_filter( 'the_content', 'eypd_content' );
