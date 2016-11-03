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
define("CLOSENESS", 5);

/**
 * Load up our scripts
 *
 */
function eypd_load_scripts() {
	$template_dir = get_stylesheet_directory_uri();

	// toss Events Manager scripts and their dependancies
	wp_dequeue_script( 'events-manager' );

	// replace script from theme
	// wp_enqueue_script('events-manager', plugins_url('assets/js/events-manager.js',__FILE__), array(), EM_VERSION); 

	$script_deps = array(
		'jquery'=>'jquery',
		'jquery-ui-core'=>'jquery-ui-core',
		'jquery-ui-widget'=>'jquery-ui-widget',
		'jquery-ui-position'=>'jquery-ui-position',
		'jquery-ui-sortable'=>'jquery-ui-sortable',
		'jquery-ui-datepicker'=>'jquery-ui-datepicker',
		'jquery-ui-autocomplete'=>'jquery-ui-autocomplete',
		'jquery-ui-dialog'=>'jquery-ui-dialog'
	);

	wp_enqueue_script('events-manager', $template_dir . '/assets/js/events-manager.js', array_values($script_deps), EM_VERSION);

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

// changing state to province
update_option( 'dbem_search_form_state_label', 'Province' );

// changing state to province
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


/*
|--------------------------------------------------------------------------
| Maps
|--------------------------------------------------------------------------
|
| Hijacks em-actions.php from events-manager plugin
|
|
*/

function eypd_init_actions() {
	global $wpdb,$EM_Notices,$EM_Event,$closeness; 

	update_option ( 'dbem_location_event_list_item_format', '<li class="category-#_EVENTPOSTID">#_EVENTLINK - #_EVENTDATES - #_EVENTTIMES</li>', TRUE );

	if( defined('DOING_AJAX') && DOING_AJAX ) $_REQUEST['em_ajax'] = true;
	
	//NOTE - No EM objects are globalized at this point, as we're hitting early init mode.
	//TODO Clean this up.... use a uniformed way of calling EM Ajax actions
	if( !empty($_REQUEST['em_ajax']) || !empty($_REQUEST['em_ajax_action']) ){
		if(isset($_REQUEST['em_ajax_action']) && $_REQUEST['em_ajax_action'] == 'get_location') {
			if(isset($_REQUEST['id'])){
				$EM_Location = new EM_Location($_REQUEST['id'], 'location_id');
				$location_array = $EM_Location->to_array();
				$location_array['location_balloon'] = $EM_Location->output(get_option('dbem_location_baloon_format'));
		     	echo EM_Object::json_encode($location_array);
			}
			die();
		}   

		if(isset($_REQUEST['query']) && $_REQUEST['query'] == 'GlobalMapData') {
			$EM_Locations = EM_Locations::get( $_REQUEST );
			$json_locations = array();
			$group_key = 0;
	
			// gather the locations
			foreach($EM_Locations as $location_key => $EM_Location) {
				$json_locations[$location_key] = $EM_Location->to_array();

				$eypd_edit = $EM_Location->output(get_option('dbem_map_text_format'));
				$eypd_edit = eypd_event_etc_output($eypd_edit);

				$json_locations[$location_key]['location_balloon'] = $eypd_edit;

				// toss venues without events
				if ((substr_count($eypd_edit, '<li') < 2) && (substr_count($eypd_edit, 'No events in this location') > 0)) {
					unset($json_locations[$location_key]);
				}
				else {
					// only need to fire if its being used
					if ($location_key > $group_key) {
						$group_key = $location_key; 
					}
				}
			}

			$location_size = sizeof($json_locations);
			while ($location_size > $cluster_size) {
				$location_size = sizeof($json_locations);
				list($json_locations, $group_key) = eypd_cluster_locations($json_locations, $group_key);
				$cluster_size = sizeof($json_locations);

				// loop until the location stops shrinking from clustering
			}
			$json_locations = array_filter($json_locations);
			$output = 0;
			foreach ($json_locations as $json_location) {
				$json_location_output[$output++] = $json_location;
			}


			echo EM_Object::json_encode($json_location_output);
		 	die();   
	 	}
	
		if(isset($_REQUEST['ajaxCalendar']) && $_REQUEST['ajaxCalendar']) {
			//FIXME if long events enabled originally, this won't show up on ajax call
			echo EM_Calendar::output($_REQUEST, false);
			die();
		}
	}
	
	//Event Actions
	if( !empty($_REQUEST['action']) && substr($_REQUEST['action'],0,5) == 'event' ){
		//Load the event object, with saved event if requested
		if( !empty($_REQUEST['event_id']) ){
			$EM_Event = new EM_Event($_REQUEST['event_id']);
		}else{
			$EM_Event = new EM_Event();
		}
		//Save Event, only via BP or via [event_form]
		if( $_REQUEST['action'] == 'event_save' && $EM_Event->can_manage('edit_events','edit_others_events') ){
			//Check Nonces
			if( !wp_verify_nonce($_REQUEST['_wpnonce'], 'wpnonce_event_save') ) exit('Trying to perform an illegal action.');
			//Grab and validate submitted data
			if ( $EM_Event->get_post() && $EM_Event->save() ) { //EM_Event gets the event if submitted via POST and validates it (safer than to depend on JS)
				$events_result = true;
				//Success notice
				if( is_user_logged_in() ){
					if( empty($_REQUEST['event_id']) ){
						$EM_Notices->add_confirm( $EM_Event->output(get_option('dbem_events_form_result_success')), true);
					}else{
					    $EM_Notices->add_confirm( $EM_Event->output(get_option('dbem_events_form_result_success_updated')), true);
					}
				}else{
					$EM_Notices->add_confirm( $EM_Event->output(get_option('dbem_events_anonymous_result_success')), true);
				}
				$redirect = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : em_wp_get_referer();
				$redirect = em_add_get_params($redirect, array('success'=>1), false, false);
				wp_redirect( $redirect );
				exit();
			}else{
				$EM_Notices->add_error( $EM_Event->get_errors() );
				$events_result = false;				
			}
		}
		if ( $_REQUEST['action'] == 'event_duplicate' && wp_verify_nonce($_REQUEST['_wpnonce'],'event_duplicate_'.$EM_Event->event_id) ) {
			$event = $EM_Event->duplicate();
			if( $event === false ){
				$EM_Notices->add_error($EM_Event->errors, true);
				wp_redirect( em_wp_get_referer() );
			}else{
				$EM_Notices->add_confirm($EM_Event->feedback_message, true);
				wp_redirect( $event->get_edit_url() );
			}
			exit();
		}
		if ( $_REQUEST['action'] == 'event_delete' && wp_verify_nonce($_REQUEST['_wpnonce'],'event_delete_'.$EM_Event->event_id) ) { 
			//DELETE action
			$selectedEvents = !empty($_REQUEST['events']) ? $_REQUEST['events']:'';
			if(  EM_Object::array_is_numeric($selectedEvents) ){
				$events_result = EM_Events::delete( $selectedEvents );
			}elseif( is_object($EM_Event) ){
				$events_result = $EM_Event->delete();
			}		
			$plural = (count($selectedEvents) > 1) ? __('Events','events-manager'):__('Event','events-manager');
			if($events_result){
				$message = ( !empty($EM_Event->feedback_message) ) ? $EM_Event->feedback_message : sprintf(__('%s successfully deleted.','events-manager'),$plural);
				$EM_Notices->add_confirm( $message, true );
			}else{
				$message = ( !empty($EM_Event->errors) ) ? $EM_Event->errors : sprintf(__('%s could not be deleted.','events-manager'),$plural);
				$EM_Notices->add_error( $message, true );		
			}
			wp_redirect( em_wp_get_referer() );
			exit();
		}elseif( $_REQUEST['action'] == 'event_detach' && wp_verify_nonce($_REQUEST['_wpnonce'],'event_detach_'.get_current_user_id().'_'.$EM_Event->event_id) ){ 
			//Detach event and move on
			if($EM_Event->detach()){
				$EM_Notices->add_confirm( $EM_Event->feedback_message, true );
			}else{
				$EM_Notices->add_error( $EM_Event->errors, true );			
			}
			wp_redirect(em_wp_get_referer());
			exit();
		}elseif( $_REQUEST['action'] == 'event_attach' && !empty($_REQUEST['undo_id']) && wp_verify_nonce($_REQUEST['_wpnonce'],'event_attach_'.get_current_user_id().'_'.$EM_Event->event_id) ){ 
			//Detach event and move on
			if($EM_Event->attach($_REQUEST['undo_id'])){
				$EM_Notices->add_confirm( $EM_Event->feedback_message, true );
			}else{
				$EM_Notices->add_error( $EM_Event->errors, true );
			}
			wp_redirect(em_wp_get_referer());
			exit();
		}
		
		//AJAX Exit
		if( isset($events_result) && !empty($_REQUEST['em_ajax']) ){
			if( $events_result ){
				$return = array('result'=>true, 'message'=>$EM_Event->feedback_message);
			}else{		
				$return = array('result'=>false, 'message'=>$EM_Event->feedback_message, 'errors'=>$EM_Event->errors);
			}
			echo EM_Object::json_encode($return);
			edit();
		}
	}
	
	//Location Actions
	if( !empty($_REQUEST['action']) && substr($_REQUEST['action'],0,8) == 'location' ){
		global $EM_Location, $EM_Notices;
		//Load the location object, with saved event if requested
		if( !empty($_REQUEST['location_id']) ){
			$EM_Location = new EM_Location($_REQUEST['location_id']);
		}else{
			$EM_Location = new EM_Location();
		}
		if( $_REQUEST['action'] == 'location_save' && $EM_Location->can_manage('edit_locations','edit_others_locations') ){
			//Check Nonces
			em_verify_nonce('location_save');
			//Grab and validate submitted data
			if ( $EM_Location->get_post() && $EM_Location->save() ) { //EM_location gets the location if submitted via POST and validates it (safer than to depend on JS)
				$EM_Notices->add_confirm($EM_Location->feedback_message, true);
				$redirect = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : em_wp_get_referer();
				wp_redirect( $redirect );
				exit();
			}else{
				$EM_Notices->add_error( $EM_Location->get_errors() );
				$result = false;		
			}
		}elseif( !empty($_REQUEST['action']) && $_REQUEST['action'] == "location_delete" ){
			//delete location
			//get object or objects			
			if( !empty($_REQUEST['locations']) || !empty($_REQUEST['location_id']) ){
				$args = !empty($_REQUEST['locations']) ? $_REQUEST['locations']:$_REQUEST['location_id'];
				$locations = EM_Locations::get($args);
				foreach($locations as $location) {
					if( !$location->delete() ){
						$EM_Notices->add_error($location->get_errors());
						$errors = true;
					}			
				}
				if( empty($errors) ){
					$result = true;
					$location_term = ( count($locations) > 1 ) ?__('Locations', 'events-manager') : __('Location', 'events-manager'); 
					$EM_Notices->add_confirm( sprintf(__('%s successfully deleted', 'events-manager'), $location_term) );
				}else{
					$result = false;
				}
			}
		}elseif( !empty($_REQUEST['action']) && $_REQUEST['action'] == "locations_search" && (!empty($_REQUEST['term']) || !empty($_REQUEST['q'])) ){
			$results = array();
			if( is_user_logged_in() || ( get_option('dbem_events_anonymous_submissions') && user_can(get_option('dbem_events_anonymous_user'), 'read_others_locations') ) ){
				$location_cond = (is_user_logged_in() && !current_user_can('read_others_locations')) ? "AND location_owner=".get_current_user_id() : '';
				if( !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') ){
					if( !user_can(get_option('dbem_events_anonymous_user'),'read_private_locations') ){
						$location_cond = " AND location_private=0";	
					}
				}elseif( is_user_logged_in() && !current_user_can('read_private_locations') ){
				    $location_cond = " AND location_private=0";
				}elseif( !is_user_logged_in() ){
					$location_cond = " AND location_private=0";		    
				}
				$location_cond = apply_filters('em_actions_locations_search_cond', $location_cond);
				$term = (isset($_REQUEST['term'])) ? '%'.$wpdb->esc_like(wp_unslash($_REQUEST['term'])).'%' : '%'.$wpdb->esc_like(wp_unslash($_REQUEST['q'])).'%';
				$sql = $wpdb->prepare("
					SELECT 
						location_id AS `id`,
						Concat( location_name )  AS `label`,
						location_name AS `value`,
						location_address AS `address`, 
						location_town AS `town`, 
						location_state AS `state`,
						location_region AS `region`,
						location_postcode AS `postcode`,
						location_country AS `country`
					FROM ".EM_LOCATIONS_TABLE." 
					WHERE ( `location_name` LIKE %s ) AND location_status=1 $location_cond LIMIT 10
				", $term);
				$results = $wpdb->get_results($sql);
			}
			echo EM_Object::json_encode($results);
			die();
		}
		if( isset($result) && $result && !empty($_REQUEST['em_ajax']) ){
			$return = array('result'=>true, 'message'=>$EM_Location->feedback_message);
			echo EM_Object::json_encode($return);
			die();
		}elseif( isset($result) && !$result && !empty($_REQUEST['em_ajax']) ){
			$return = array('result'=>false, 'message'=>$EM_Location->feedback_message, 'errors'=>$EM_Notices->get_errors());
			echo EM_Object::json_encode($return);
			die();
		}
	}
	
	//Booking Actions
	if( !empty($_REQUEST['action']) && substr($_REQUEST['action'],0,7) == 'booking' && (is_user_logged_in() || ($_REQUEST['action'] == 'booking_add' && get_option('dbem_bookings_anonymous'))) ){
		global $EM_Event, $EM_Booking, $EM_Person;
		//Load the booking object, with saved booking if requested
		$EM_Booking = ( !empty($_REQUEST['booking_id']) ) ? em_get_booking($_REQUEST['booking_id']) : em_get_booking();
		if( !empty($EM_Booking->event_id) ){
			//Load the event object, with saved event if requested
			$EM_Event = $EM_Booking->get_event();
		}elseif( !empty($_REQUEST['event_id']) ){
			$EM_Event = new EM_Event($_REQUEST['event_id']);
		}
		$allowed_actions = array('bookings_approve'=>'approve','bookings_reject'=>'reject','bookings_unapprove'=>'unapprove', 'bookings_delete'=>'delete');
		$result = false;
		$feedback = '';
		if ( $_REQUEST['action'] == 'booking_add') {
			//ADD/EDIT Booking
			ob_start();
			if( !defined('WP_CACHE') || !WP_CACHE ) em_verify_nonce('booking_add');
			if( !is_user_logged_in() || get_option('dbem_bookings_double') || !$EM_Event->get_bookings()->has_booking(get_current_user_id()) ){
			    $EM_Booking->get_post();
				$post_validation = $EM_Booking->validate();
				do_action('em_booking_add', $EM_Event, $EM_Booking, $post_validation);
				if( $post_validation ){
				    //register the user - or not depending - according to the booking
				    $registration = em_booking_add_registration($EM_Booking);
					$EM_Bookings = $EM_Event->get_bookings();
					if( $registration && $EM_Bookings->add($EM_Booking) ){
					    if( is_user_logged_in() && is_multisite() && !is_user_member_of_blog(get_current_user_id(), get_current_blog_id()) ){
					        add_user_to_blog(get_current_blog_id(), get_current_user_id(), get_option('default_role'));
					    }
						$result = true;
						$EM_Notices->add_confirm( $EM_Bookings->feedback_message );		
						$feedback = $EM_Bookings->feedback_message;
					}else{
						$result = false;
						if(!$registration){
						    $EM_Notices->add_error( $EM_Booking->get_errors() );
							$feedback = $EM_Booking->feedback_message;
						}else{
						    $EM_Notices->add_error( $EM_Bookings->get_errors() );
							$feedback = $EM_Bookings->feedback_message;
						}				
					}
					global $em_temp_user_data; $em_temp_user_data = false; //delete registered user temp info (if exists)
				}else{
					$result = false;
					$EM_Notices->add_error( $EM_Booking->get_errors() );
				}
			}else{
				$result = false;
				$feedback = get_option('dbem_booking_feedback_already_booked');
				$EM_Notices->add_error( $feedback );
			}
			ob_clean();
	  	}elseif ( $_REQUEST['action'] == 'booking_add_one' && is_object($EM_Event) && is_user_logged_in() ) {
			//ADD/EDIT Booking
			em_verify_nonce('booking_add_one');
			if( !$EM_Event->get_bookings()->has_booking(get_current_user_id()) || get_option('dbem_bookings_double')){
				$EM_Booking = em_get_booking(array('person_id'=>get_current_user_id(), 'event_id'=>$EM_Event->event_id, 'booking_spaces'=>1)); //new booking
				$EM_Ticket = $EM_Event->get_bookings()->get_tickets()->get_first();	
				//get first ticket in this event and book one place there. similar to getting the form values in EM_Booking::get_post_values()
				$EM_Ticket_Booking = new EM_Ticket_Booking(array('ticket_id'=>$EM_Ticket->ticket_id, 'ticket_booking_spaces'=>1));
				$EM_Booking->tickets_bookings = new EM_Tickets_Bookings();
				$EM_Booking->tickets_bookings->booking = $EM_Ticket_Booking->booking = $EM_Booking;
				$EM_Booking->tickets_bookings->add( $EM_Ticket_Booking );
				//Now save booking
				if( $EM_Event->get_bookings()->add($EM_Booking) ){
					$result = true;
					$EM_Notices->add_confirm( $EM_Event->get_bookings()->feedback_message );		
					$feedback = $EM_Event->get_bookings()->feedback_message;	
				}else{
					$result = false;
					$EM_Notices->add_error( $EM_Event->get_bookings()->get_errors() );			
					$feedback = $EM_Event->get_bookings()->feedback_message;	
				}
			}else{
				$result = false;
				$feedback = get_option('dbem_booking_feedback_already_booked');
				$EM_Notices->add_error( $feedback );
			}
	  	}elseif ( $_REQUEST['action'] == 'booking_cancel') {
	  		//Cancel Booking
			em_verify_nonce('booking_cancel');
	  		if( $EM_Booking->can_manage() || ($EM_Booking->person->ID == get_current_user_id() && get_option('dbem_bookings_user_cancellation')) ){
				if( $EM_Booking->cancel() ){
					$result = true;
					if( !defined('DOING_AJAX') ){
						if( $EM_Booking->person->ID == get_current_user_id() ){
							$EM_Notices->add_confirm(get_option('dbem_booking_feedback_cancelled'), true );	
						}else{
							$EM_Notices->add_confirm( $EM_Booking->feedback_message, true );
						}
						wp_redirect( $_SERVER['HTTP_REFERER'] );
						exit();
					}
				}else{
					$result = false;
					$EM_Notices->add_error( $EM_Booking->get_errors() );
					$feedback = $EM_Booking->feedback_message;
				}
			}else{
				$EM_Notices->add_error( __('You must log in to cancel your booking.', 'events-manager') );
			}
		//TODO user action shouldn't check permission, booking object should.
	  	}elseif( array_key_exists($_REQUEST['action'], $allowed_actions) && $EM_Event->can_manage('manage_bookings','manage_others_bookings') ){
	  		//Event Admin only actions
			$action = $allowed_actions[$_REQUEST['action']];
			//Just do it here, since we may be deleting bookings of different events.
			if( !empty($_REQUEST['bookings']) && EM_Object::array_is_numeric($_REQUEST['bookings'])){
				$results = array();
				foreach($_REQUEST['bookings'] as $booking_id){
					$EM_Booking = em_get_booking($booking_id);
					$result = $EM_Booking->$action();
					$results[] = $result;
					if( !in_array(false, $results) && !$result ){
						$feedback = $EM_Booking->feedback_message;
					}
				}
				$result = !in_array(false,$results);
			}elseif( is_object($EM_Booking) ){
				$result = $EM_Booking->$action();
				$feedback = $EM_Booking->feedback_message;
			}
			//FIXME not adhereing to object's feedback or error message, like other bits in this file.
			//TODO multiple deletion won't work in ajax
			if( !empty($_REQUEST['em_ajax']) ){
				if( $result ){
					echo $feedback;
				}else{
					echo '<span style="color:red">'.$feedback.'</span>';
				}	
				die();
			}else{
			    if( $result ){
			        $EM_Notices->add_confirm($feedback);
			    }else{
			        $EM_Notices->add_error($feedback);
			    }
			}
		}elseif( $_REQUEST['action'] == 'booking_save' ){
			em_verify_nonce('booking_save_'.$EM_Booking->booking_id);
			do_action('em_booking_save', $EM_Event, $EM_Booking);
			if( $EM_Booking->can_manage('manage_bookings','manage_others_bookings') ){
				if ($EM_Booking->get_post(true) && $EM_Booking->validate(true) && $EM_Booking->save(false) ){
					$EM_Notices->add_confirm( $EM_Booking->feedback_message, true );
					$redirect = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : em_wp_get_referer();
					wp_redirect( $redirect );
					exit();
				}else{
					$result = false;
					$EM_Notices->add_error( $EM_Booking->get_errors() );			
					$feedback = $EM_Booking->feedback_message;	
				}	
			}
		}elseif( $_REQUEST['action'] == 'booking_set_status' ){
			em_verify_nonce('booking_set_status_'.$EM_Booking->booking_id);
			if( $EM_Booking->can_manage('manage_bookings','manage_others_bookings') && $_REQUEST['booking_status'] != $EM_Booking->booking_status ){
				if ( $EM_Booking->set_status($_REQUEST['booking_status'], false, true) ){
					if( !empty($_REQUEST['send_email']) ){
						if( $EM_Booking->email() ){
						    if( $EM_Booking->mails_sent > 0 ) {
						        $EM_Booking->feedback_message .= " ".__('Email Sent.','events-manager');
						    }else{
						        $EM_Booking->feedback_message .= " "._x('No emails to send for this booking.', 'bookings', 'events-manager');
						    }
						}else{
							$EM_Booking->feedback_message .= ' <span style="color:red">'.__('ERROR : Email Not Sent.','events-manager').'</span>';
						}
					}
					$EM_Notices->add_confirm( $EM_Booking->feedback_message, true );
					$redirect = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : em_wp_get_referer();
					wp_redirect( $redirect );
					exit();
				}else{
					$result = false;
					$EM_Notices->add_error( $EM_Booking->get_errors() );
					$feedback = $EM_Booking->feedback_message;	
				}	
			}
		}elseif( $_REQUEST['action'] == 'booking_resend_email' ){
			em_verify_nonce('booking_resend_email_'.$EM_Booking->booking_id);
			if( $EM_Booking->can_manage('manage_bookings','manage_others_bookings') ){
				if( $EM_Booking->email(false, true) ){
				    if( $EM_Booking->mails_sent > 0 ) {
				        $EM_Notices->add_confirm( __('Email Sent.','events-manager'), true );
				    }else{
				        $EM_Notices->add_confirm( _x('No emails to send for this booking.', 'bookings', 'events-manager'), true );
				    }
					$redirect = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : em_wp_get_referer();
					wp_redirect( $redirect );
					exit();
				}else{
					$result = false;
					$EM_Notices->add_error( __('ERROR : Email Not Sent.','events-manager') );			
					$feedback = $EM_Booking->feedback_message;
				}	
			}
		}elseif( $_REQUEST['action'] == 'booking_modify_person' ){
			em_verify_nonce('booking_modify_person_'.$EM_Booking->booking_id);
			if( $EM_Booking->can_manage('manage_bookings','manage_others_bookings') ){
			    global $wpdb;
			    $no_user = get_option('dbem_bookings_registration_disable') && $EM_Booking->get_person()->ID == get_option('dbem_bookings_registration_user');
				if( //save just the booking meta, avoid extra unneccesary hooks and things to go wrong
					$no_user && $EM_Booking->get_person_post() && 
			    	$wpdb->update(EM_BOOKINGS_TABLE, array('booking_meta'=> serialize($EM_Booking->booking_meta)), array('booking_id'=>$EM_Booking->booking_id))
				){
					$EM_Notices->add_confirm( $EM_Booking->feedback_message, true );
					$redirect = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : em_wp_get_referer();
					wp_redirect( $redirect );
					exit();
				}else{
					$result = false;
					$EM_Notices->add_error( $EM_Booking->get_errors() );			
					$feedback = $EM_Booking->feedback_message;	
				}	
			}
			do_action('em_booking_modify_person', $EM_Event, $EM_Booking);
		}elseif( $_REQUEST['action'] == 'bookings_add_note' && $EM_Booking->can_manage('manage_bookings','manage_others_bookings') ) {
			em_verify_nonce('bookings_add_note');
			if( $EM_Booking->add_note(wp_unslash($_REQUEST['booking_note'])) ){
				$EM_Notices->add_confirm($EM_Booking->feedback_message, true);
				$redirect = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : em_wp_get_referer();
				wp_redirect( $redirect );
				exit();
			}else{
				$EM_Notices->add_error($EM_Booking->errors);
			}
		}
	
		if( $result && defined('DOING_AJAX') ){
			$return = array('result'=>true, 'message'=>$feedback);
			header( 'Content-Type: application/javascript; charset=UTF-8', true ); //add this for HTTP -> HTTPS requests which assume it's a cross-site request
			echo EM_Object::json_encode(apply_filters('em_action_'.$_REQUEST['action'], $return, $EM_Booking));
			die();
		}elseif( !$result && defined('DOING_AJAX') ){
			$return = array('result'=>false, 'message'=>$feedback, 'errors'=>$EM_Notices->get_errors());
			header( 'Content-Type: application/javascript; charset=UTF-8', true ); //add this for HTTP -> HTTPS requests which assume it's a cross-site request
			echo EM_Object::json_encode(apply_filters('em_action_'.$_REQUEST['action'], $return, $EM_Booking));
			die();
		}
	}elseif( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'booking_add' && !is_user_logged_in() && !get_option('dbem_bookings_anonymous')){
		$EM_Notices->add_error( get_option('dbem_booking_feedback_log_in') );
		if( !$result && defined('DOING_AJAX') ){
			$return = array('result'=>false, 'message'=>$EM_Booking->feedback_message, 'errors'=>$EM_Notices->get_errors());
			echo EM_Object::json_encode(apply_filters('em_action_'.$_REQUEST['action'], $return, $EM_Booking));
		}
		die();
	}
	
	//AJAX call for searches
	if( !empty($_REQUEST['action']) && substr($_REQUEST['action'],0,6) == 'search' ){
		//default search arts
		if( $_REQUEST['action'] == 'search_states' ){
			$results = array();
			$conds = array();
			if( !empty($_REQUEST['country']) ){
				$conds[] = $wpdb->prepare("(location_country = '%s' OR location_country IS NULL )", $_REQUEST['country']);
			}
			if( !empty($_REQUEST['region']) ){
				$conds[] = $wpdb->prepare("( location_region = '%s' )", $_REQUEST['region']);
			}
			$cond = (count($conds) > 0) ? "AND ".implode(' AND ', $conds):'';
			$results = $wpdb->get_col("SELECT DISTINCT location_state FROM " . EM_LOCATIONS_TABLE ." WHERE location_state IS NOT NULL AND location_state != '' $cond ORDER BY location_state");
			if( $_REQUEST['return_html'] ) {
				//quick shortcut for quick html form manipulation
				ob_start();
				?>
				<option value=''><?php echo get_option('dbem_search_form_states_label') ?></option>
				<?php
				foreach( $results as $result ){
					echo "<option>{$result}</option>";
				}
				$return = ob_get_clean();
				echo apply_filters('em_ajax_search_states', $return);
				exit();
			}else{
				echo EM_Object::json_encode($results);
				exit();
			}
		}
		if( $_REQUEST['action'] == 'search_towns' ){
			$results = array();
			$conds = array();
			if( !empty($_REQUEST['country']) ){
				$conds[] = $wpdb->prepare("(location_country = '%s' OR location_country IS NULL )", $_REQUEST['country']);
			}
			if( !empty($_REQUEST['region']) ){
				$conds[] = $wpdb->prepare("( location_region = '%s' )", $_REQUEST['region']);
			}
			if( !empty($_REQUEST['state']) ){
				$conds[] = $wpdb->prepare("(location_state = '%s' )", $_REQUEST['state']);
			}
			$cond = (count($conds) > 0) ? "AND ".implode(' AND ', $conds):'';
			$results = $wpdb->get_col("SELECT DISTINCT location_town FROM " . EM_LOCATIONS_TABLE ." WHERE location_town IS NOT NULL AND location_town != '' $cond  ORDER BY location_town");
			if( $_REQUEST['return_html'] ) {
				//quick shortcut for quick html form manipulation
				ob_start();
				?>
				<option value=''><?php echo get_option('dbem_search_form_towns_label'); ?></option>
				<?php			
				foreach( $results as $result ){
					echo "<option>$result</option>";
				}
				$return = ob_get_clean();
				echo apply_filters('em_ajax_search_towns', $return);
				exit();
			}else{
				echo EM_Object::json_encode($results);
				exit();
			}
		}
		if( $_REQUEST['action'] == 'search_regions' ){
			$results = array();
			if( !empty($_REQUEST['country']) ){
				$conds[] = $wpdb->prepare("(location_country = '%s' )", $_REQUEST['country']);
			}
			$cond = (count($conds) > 0) ? "AND ".implode(' AND ', $conds):'';
			$results = $wpdb->get_results("SELECT DISTINCT location_region AS value FROM " . EM_LOCATIONS_TABLE ." WHERE location_region IS NOT NULL AND location_region != '' $cond  ORDER BY location_region");
			if( $_REQUEST['return_html'] ) {
				//quick shortcut for quick html form manipulation
				ob_start();
				?>
				<option value=''><?php echo get_option('dbem_search_form_regions_label'); ?></option>
				<?php	
				foreach( $results as $result ){
					echo "<option>{$result->value}</option>";
				}
				$return = ob_get_clean();
				echo apply_filters('em_ajax_search_regions', $return);
				exit();
			}else{
				echo EM_Object::json_encode($results);
				exit();
			}
		}
	}
		
	//EM Ajax requests require this flag.
	if( is_user_logged_in() ){
		//Admin operations
		//Specific Oject Ajax
		if( !empty($_REQUEST['em_obj']) ){
			switch( $_REQUEST['em_obj'] ){
				case 'em_bookings_events_table':
					include_once('admin/bookings/em-events.php');
					em_bookings_events_table();
					exit();
					break;
				case 'em_bookings_pending_table':
					include_once('admin/bookings/em-pending.php');
					em_bookings_pending_table();
					exit();
					break;
				case 'em_bookings_confirmed_table':
					//add some admin files just in case
					include_once('admin/bookings/em-confirmed.php');
					em_bookings_confirmed_table();
					exit();
					break;
			}
		}
	}
	//Export CSV - WIP
	if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'export_bookings_csv' && wp_verify_nonce($_REQUEST['_wpnonce'], 'export_bookings_csv')){
		if( !empty($_REQUEST['event_id']) ){
			$EM_Event = em_get_event($_REQUEST['event_id']);
		}
		//sort out cols
		if( !empty($_REQUEST['cols']) && is_array($_REQUEST['cols']) ){
			$cols = array();
			foreach($_REQUEST['cols'] as $col => $active){
				if( $active ){ $cols[] = $col; }
			}
			$_REQUEST['cols'] = $cols;
		}
		$_REQUEST['limit'] = 0;
		
		//generate bookings export according to search request
		$show_tickets = !empty($_REQUEST['show_tickets']);
		$EM_Bookings_Table = new EM_Bookings_Table($show_tickets);
		header("Content-Type: application/octet-stream; charset=utf-8");
		$file_name = !empty($EM_Event->event_slug) ? $EM_Event->event_slug:get_bloginfo();
		header("Content-Disposition: Attachment; filename=".sanitize_title($file_name)."-bookings-export.csv");
		do_action('em_csv_header_output');
		echo "\xEF\xBB\xBF"; // UTF-8 for MS Excel (a little hacky... but does the job)
		if( !defined('EM_CSV_DISABLE_HEADERS') || !EM_CSV_DISABLE_HEADERS ){
			if( !empty($_REQUEST['event_id']) ){
				echo __('Event','events-manager') . ' : ' . $EM_Event->event_name .  "\n";
				if( $EM_Event->location_id > 0 ) echo __('Where','events-manager') . ' - ' . $EM_Event->get_location()->location_name .  "\n";
				echo __('When','events-manager') . ' : ' . $EM_Event->output('#_EVENTDATES - #_EVENTTIMES') .  "\n";
			}
			echo sprintf(__('Exported booking on %s','events-manager'), date_i18n('D d M Y h:i', current_time('timestamp'))) .  "\n";
		}
		$delimiter = !defined('EM_CSV_DELIMITER') ? ',' : EM_CSV_DELIMITER;
		$delimiter = apply_filters('em_csv_delimiter', $delimiter);
		//Rows
		$EM_Bookings_Table->limit = 150; //if you're having server memory issues, try messing with this number
		$EM_Bookings = $EM_Bookings_Table->get_bookings();
		$handle = fopen("php://output", "w");
		fputcsv($handle, $EM_Bookings_Table->get_headers(true), $delimiter);
		while(!empty($EM_Bookings->bookings)){
			foreach( $EM_Bookings->bookings as $EM_Booking ) {
				//Display all values
				/* @var $EM_Booking EM_Booking */
				/* @var $EM_Ticket_Booking EM_Ticket_Booking */
				if( $show_tickets ){
					foreach($EM_Booking->get_tickets_bookings()->tickets_bookings as $EM_Ticket_Booking){
						$row = $EM_Bookings_Table->get_row_csv($EM_Ticket_Booking);
						fputcsv($handle, $row, $delimiter);
					}
				}else{
					$row = $EM_Bookings_Table->get_row_csv($EM_Booking);
					fputcsv($handle, $row, $delimiter);
				}
			}
			//reiterate loop
			$EM_Bookings_Table->offset += $EM_Bookings_Table->limit;
			$EM_Bookings = $EM_Bookings_Table->get_bookings();
		}
		fclose($handle);
		exit();
	}
}  

add_action('init','eypd_init_actions',10);



/**
 * Queries database for all posts that have post_meta
 * with a key = '_location_id'
 *
 * @return array
 */
function eypd_get_unique_location_id($sets = array('loc_post_id')) {
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
	if (in_array('loc_post_id', $sets)) {
		$output['loc_post_id'] = $loc_post_id;
	}
	if (in_array('loc_id', $sets)) {
		$output['loc_id'] = $loc_id;
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
	return get_post_custom($post_id);
}

function eypd_event_output($post_id = 0, $data = array()) {
	// get the data
	if ($data === array()) {
		$data = eypd_event_data($post_id);
	}
	// get the design

	// return the design
	return $data;

	return $output;
}

/* intercepts output, finds post id#s, uses them to get slugs and insert those slugs into the <li> as classes */
function eypd_event_etc_output($input = "") {
	$output = $input;
	preg_match_all("/<li class=\"category-(\d+)\">/", $input, $output_array);
	foreach ($output_array[1] as $index => $post_id) {
		$cats = wp_get_object_terms( $post_id, 'event-categories' );
		$cat_output = $space = "";
		foreach ($cats as $cat) {
			$c = get_category( $cat );
			$cat_output .= $space."cat_".str_replace("-","_", $c->slug);
			$space = " ";
		}
		$new_classes = "<li class=\"$cat_output\">";
		$output = str_replace($output_array[0][$index], $new_classes, $output);
	}
	return $output;
}


function eypd_cluster_locations($json_locations, $group_key) {
	// iterate through the positions
	foreach ($json_locations as $location_key => $location_array) {
		// pull those that are close and group them
		foreach ($json_locations as $compare_key => $compare_array) {
			if (($compare_key != $location_key) && (eypd_distance($location_array['location_latitude'], $location_array['location_longitude'], $compare_array['location_latitude'], $compare_array['location_longitude']) < constant("CLOSENESS"))) {
				$group_key++;
				// pull the location_key, then the compare_key, merge them into one larger balloon and add to the $json_locations_grouped

				$json_locations[$group_key] = $location_array;
				$json_locations[$group_key]['location_name'] = $location_array['location_town'];
				$json_locations[$group_key]['location_latitude'] = floatval(($location_array['location_latitude'] + $compare_array['location_latitude']) / 2);
				$json_locations[$group_key]['location_longitude'] = floatval(($location_array['location_longitude'] + $compare_array['location_longitude']) / 2);

				// name
				// slug
				// address
				// town
				// state

				
				$address = '<span style="display: block;">';
				$address .= (strlen($json_locations[$location_key]['location_name']) > 1) ? "<strong>".$json_locations[$location_key]['location_name']."</strong><br/>" : "";
				$address .= (strlen($json_locations[$location_key]['location_address']) > 1) ? $json_locations[$location_key]['location_address']."<br/>" : "";
				$address .= (strlen($json_locations[$location_key]['location_town']) > 1) ? $json_locations[$location_key]['location_town'] : "";
				$address .= (strlen($json_locations[$location_key]['location_state']) > 0) ? ", ".$json_locations[$location_key]['location_state'] : "";
				$address .= "</span>";

				$json_locations[$group_key]['location_balloon'] = str_replace($address, "", $json_locations[$group_key]['location_balloon']);
				$json_locations[$group_key]['location_balloon'] = "<span>".$json_locations[$location_key]['location_balloon']."</span>";
				
				// recycle the variable $address

				$address = '<span style="display: block;">';
				$address .= (strlen($json_locations[$compare_key]['location_name']) > 1) ? "<strong>".$json_locations[$compare_key]['location_name']."</strong><br/>" : "";
				$address .= (strlen($json_locations[$compare_key]['location_address']) > 1) ? $json_locations[$compare_key]['location_address']."<br/>" : "";
				$address .= (strlen($json_locations[$compare_key]['location_town']) > 1) ? $json_locations[$compare_key]['location_town'] : "";
				$address .= (strlen($json_locations[$compare_key]['location_state']) > 0) ? ", ".$json_locations[$compare_key]['location_state'] : "";
				$address .= "</span>";

				$json_locations[$group_key]['location_balloon'] = str_replace($address, "", $json_locations[$group_key]['location_balloon']);
				$json_locations[$group_key]['location_balloon'] .= "<span>".$json_locations[$compare_key]['location_balloon']."</span>";

				$json_locations[$group_key]['location_address'] = "";
				$json_locations[$group_key]['location_town'] = "";
				$json_locations[$group_key]['location_state'] = "";

				// toss these
				// this should destroy these but not effect that they still exist in the for next loop
				unset($json_locations[$location_key]);
				unset($json_locations[$compare_key]);
			}
		}
	}
	return array($json_locations, $group_key);
}

add_action('wp_ajax_nopriv_cyop_lookup', 'et_fetch');
add_action('wp_ajax_cyop_lookup', 'et_fetch');

// use it for two uses -- the Ajax response and th post info
function et_fetch($post_id = -1, $ajax = TRUE) {
	if ($ajax == TRUE) {	
		$output = eypd_event_output($post_id);
		echo json_encode($output); //encode into JSON format and output
		die(); //stop "0" from being output
	}
}

