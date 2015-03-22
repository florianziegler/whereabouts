<?php defined( 'ABSPATH' ) OR exit;

/**
 * @package Whereabouts
 * @since 0.1.0
 */


/**
 * Add widget to the dashboard
 *
 * @since 0.1.0
 */

add_action( 'wp_dashboard_setup', 'whereabouts_add_dashboard_widget' );

function whereabouts_add_dashboard_widget() {

	// Load user roles, that are allowed to use the widget
	$settings = get_option( 'whab_settings' );

	// Get current user information
	$current_user = wp_get_current_user();
	if ( ! ( $current_user instanceof WP_User ) ) { return; }

	// Check if allowed_user_roles has been set yet, or if it indeed hasn't
	if ( ! isset( $settings['allowed_user_roles'] ) OR ( is_array( $current_user->roles ) AND is_array( $settings['allowed_user_roles'] ) ) ) {

		// Check again if allowed_user_roles has been set, or if the current user's role is among the allowed roles
		if ( ! isset( $settings['allowed_user_roles'] ) OR array_intersect( $settings['allowed_user_roles'], $current_user->roles ) ) {

			wp_add_dashboard_widget(
				'whereabouts-dashboard-widget',
				'Whereabouts (' . $current_user->display_name . ')',
				'whereabouts_build_dashboard_widget'
			);
		}
	}
}


/**
 * Build dashboard widget
 *
 * @since 0.1.0
 */

function whereabouts_build_dashboard_widget() {

	// Validate & save $_POST data, when new location info was submitted.
	if ( isset( $_REQUEST['whab_location_data'] ) ) {
		$args = $_REQUEST['whab_location_data'];
		whereabouts_validate_save_location( $args );
	} ?>

	<form method="post" action="">

	<?php
		// Get a nonce field for verifying purposes.
		wp_nonce_field();

		// Load plugin settings
		$settings = get_option( 'whab_settings' );

		// Get language
		if ( isset( $settings['language'] ) && $settings['language'] != '' ) { 
			$language = $settings['language'];
		} else { 
			$language = 'en';
		}

		// Load location data from the current user
		$current_user = wp_get_current_user();
		if ( !( $current_user instanceof WP_User ) ) { return; }
		$location = get_user_meta( $current_user->ID, 'whab_location_data', true );

		//  Add shortcut to the Whereabouts settings ?>
		<a class="whab-settings" href="options-general.php?page=whereabouts" title="<?php _e( 'Go to settings', 'whereabouts' ); ?>"><span><?php _e( 'Settings', 'whereabouts' ); ?></span></a>
		
		<?php // If the plugin is set to use Google, insert maps api script,  map canvas, a geolocation button and the geolocation box
		if ( isset( $settings['use_google'] ) AND $settings['use_google'] == true ) {  ?>

			<script src="//maps.googleapis.com/maps/api/js?v=3.exp&language=<?php echo $language; ?>"></script>
			<div id="whab-map"></div>

			<div class="whab-button-box"><a id="whab-geolocation-button" class="get-browser-geolocation" href="#"><?php _e( 'Locate yourself', 'whereabouts' ); ?></a></div>

			<p><label for="whab-location-name"><a class="get-browser-geolocation" href="#"><?php _e( "Let the browser figure out your location", "whereabouts" ); ?></a> <?php echo __( 'or', 'whereabouts' ) . ' ' . __( 'enter it manually', 'whereabouts' ); ?>:</label>
				<span class="whab-location-name-wrap">
					<input type="text" name="whab_location_data[location_name]" id="whab-location-name" value="<?php if ( isset( $location['location_name'] ) && $location['location_name'] != '' ) { echo $location['location_name']; } else { echo ''; } ?>" />
					<input type="submit" class="button button-secondary" name="get-location-data" id="whab-get-location-data" value="<?php _e( 'Get location data', 'whereabouts' ); ?>" />
				</span>
			</p>

			<div class="whab-location-box">
				<div class="whab-location-info">
					<h3><?php _e( 'Choose Location…', 'whereabouts' ); ?></h3>
					<div class="whab-loading"><span>loading</span></div>
					<ul id="geolocation-choices"></ul>
					<?php // If not set, diplay a hint, that the request language can be adjusted in the plugin's settings
					if ( ! isset( $settings['language'] ) OR $settings['language'] == '' ) { 
						_e( '<p class="whab-language-settings-hint"><strong>Tip:</strong> You can set the language in which Google returns this information in the <a href="options-general.php?page=whereabouts">settings</a>.</a>', 'whereabouts' );
					} ?>
				</div>
			</div><?php

		} else { // If Whereabouts is *not* set to use Google, a message is displayed, that says why it would be a good idea to use this feature ?>

			<div class="whab-message"><?php _e( 'Activate the use of the Google Maps API in the <a href="options-general.php?page=whereabouts">settings</a>. You will then be able to automatically set your current location and time zone. And you get a map!', 'whereabouts' ); ?></div>

			<p>
				<label for="whab-location-name">
					<?php _e( 'Enter your current location', 'whereabouts' ); ?>
				</label>
				<input type="text" name="whab_location_data[location_name]" id="whab-location-name" value="<?php if ( isset( $location['location_name'] ) && $location['location_name'] != '' ) { echo $location['location_name']; } else { echo ''; } ?>" />
			</p>

		<?php }

		// Add time zone select box ?>
		<p class="whab-col-25"><label for="whab-timezone-select"><?php _e( 'Timezone', 'whereabouts'); ?>:</label>
			<select name="whab_location_data[utc_difference]" id="whab-timezone-select">
				<?php // Add ALL the time zones!
				// https://gist.github.com/florianziegler/d735751627107d9a1926
				$timezones = array(
					'UTC-12'    => '-43200',
					'UTC-11'    => '-39600',
					'UTC-10'    => '-36000',
					'UTC-9.5'   => '-34200',
					'UTC-9'     => '-32400',
					'UTC-8'     => '-28800',
					'UTC-7'     => '-25200',
					'UTC-6'     => '-21600',
					'UTC-5'     => '-18000',
					'UTC-4.5'   => '-16200',
					'UTC-4'     => '-14400',
					'UTC-3.5'   => '-12600',
					'UTC-3'     => '-10800',
					'UTC-2'     => '-7200',
					'UTC-1'     => '-3600',
					'UTC+0'     => '0',
					'UTC+1'     => '3600',
					'UTC+2'     => '7200',
					'UTC+3'     => '10800',
					'UTC+3.5'   => '12600',
					'UTC+4'     => '14400',
					'UTC+4.5'   => '16200',
					'UTC+5'     => '18000',
					'UTC+5.5'   => '19800',
					'UTC+5.75'  => '20700',
					'UTC+6'     => '21600',
					'UTC+6.5'   => '23400',
					'UTC+7'     => '25200',
					'UTC+8'     => '28800',
					'UTC+8.75'  => '31500', // Crazy right?
					'UTC+9'     => '32400',
					'UTC+9.5'   => '34200',
					'UTC+10'    => '36000',
					'UTC+10.5'  => '37800',
					'UTC+11'    => '39600',
					'UTC+11.5'  => '41400',
					'UTC+12'    => '43200',
					'UTC+12.75' => '45900',
					'UTC+13'    => '46800',
					'UTC+14'    => '50400'
				);

				// If a time zone is already set, let's load it. If not: show UTC+0
				if ( isset( $location['utc_difference'] ) && $location['utc_difference'] != '' ) { $selected = $location['utc_difference']; } else { $selected = '0'; }

				// Generate the options
				foreach( $timezones as $key => $value ) {
					echo '<option value="' . $value . '"';
					if ( $value == $selected ) { echo ' selected="selected"'; }
					echo '>' . $key . '</option>';
				}

				?>
			</select>
		</p>
		<?php // Add time zone name input field ?>
		<p class="whab-col-75"><label for="whab-timezone-name"><?php _e( 'Timezone Name', 'whereabouts' ); ?>:</label> <input type="text" name="whab_location_data[timezone_name]" id="whab-timezone-name" value="<?php if ( isset( $location['timezone_name'] ) && $location['timezone_name'] != '' ) { echo $location['timezone_name']; } else { echo ''; } ?>" /></p>

		<?php // Add hidden geo data input field ?>
		<input type="hidden" name="whab_location_data[geo]" id="whab-geo" value="<?php if ( isset( $location['geo'] ) && $location['geo'] != '' ) { echo $location['geo']; } else { echo ''; } ?>" />

		<?php // Add save button ?>
		<p class="whab-save"><input type="submit" name="whab_save_location" id="whab-save-location" value="<?php _e( 'Save Location', 'whereabouts'); ?>" class="button button-primary" /></p>

	</form>

	<?php // If the plugin is set to use Google, load the ALL the js stuff...
		if ( isset( $settings['use_google'] ) AND $settings['use_google'] == true ) {
			add_action( 'admin_footer', 'whereabouts_action_javascript' );
		}
}


/**
 * Do js magic
 *
 * @since 0.1.0
 */

function whereabouts_action_javascript() {

	$settings = get_option( 'whab_settings' );

	// Check if the current user is actually a user, and get the meta data we need
	$current_user = wp_get_current_user();
	if ( ! ( $current_user instanceof WP_User ) ) { return; }
	$options = get_user_meta( $current_user->ID, 'whab_location_data', true );

	// Load language form the settings, use english if not set
	if ( isset( $settings['language'] ) && $settings['language'] != '' ) { 
		$language = $settings['language'];
	} else { 
		$language = 'en';
	}

	// Now then... let's do this ?>
	<script type="text/javascript" >
		jQuery( document ).ready( function( $ ) {

			// Define map and marker
			var map;
			var marker;
			var geocoder;

			$.whab = { 
				lat: '',
				long : '',
				timezone : '',
				offset : ''
			}; 

			// Initialize the map. Load geo data, if not set - use Edinburgh...
			function initialize() {
				var myLatlng = new google.maps.LatLng( <?php if ( isset( $options['geo'] ) && $options['geo'] != '' ) { echo $options['geo']; } else { echo '55.953252, -3.188267'; } ?> );
				var mapOptions = {
					// zoom: 8,
					zoom: 11,
					center: myLatlng,
					draggable: false,
					zoomControl: false,
					scrollwheel: false,
					disableDoubleClickZoom: true,
					disableDefaultUI: true
				}

				map = new google.maps.Map( document.getElementById( 'whab-map' ), mapOptions );
				marker = new google.maps.Marker({
					position: myLatlng,
					map: map
				});
				geocoder = new google.maps.Geocoder();

				// TODO: Error Handling: If geolocation is denied, or is just not working / times out

				$( '.get-browser-geolocation' ).click( function( e ){
					e.preventDefault();
					clearLB();
					$( '.whab-location-box .whab-loading' ).show();
					$( '.whab-location-box' ).show();
					
					// Do browser geolocating...
					if ( navigator.geolocation ) {
						navigator.geolocation.getCurrentPosition( function( position ) {
							initialLocation = new google.maps.LatLng( position.coords.latitude, position.coords.longitude );
							map.setCenter( initialLocation );
							marker.setPosition( initialLocation );
							geocoder.geocode({'latLng': initialLocation}, function( results, status ) {
								if ( status == google.maps.GeocoderStatus.OK ) {
									if ( results[1] ) {
										map.setZoom( 11 );

										// Add locations to the list of choices
										results.every( function( element, index, array ) {
											$( '#geolocation-choices' ).append( '<li><a class="choose-geolocation" href="#choice-' + index + '">' + element.formatted_address + '</a></li>' );
											return true;
										});

										// Store the coordinates
										$.whab.lat = position.coords.latitude;
										$.whab.long = position.coords.longitude;

										$( '.whab-location-box .whab-loading' ).hide();

									} else {
										alert( 'No results found.' );
									}
								} else {
									alert( 'Geocoder failed due to: ' + status );
								}
							});

							// Set timezone
							url = 'https://maps.googleapis.com/maps/api/timezone/json?location=' + position.coords.latitude + ',' + position.coords.longitude + '&timestamp=<?php echo time(); ?>&language=<?php echo $language; ?>';
							getTimezoneInfo( url );

						}, function() {
							// ...
						});
					}
					// Browser doesn't support Geolocation
					else {
						alert( "Sorry, your browser doesn't support geolocation." );
					}
				});
			}

			google.maps.event.addDomListener( window, 'load', initialize );

			// Oh snap. Someone pushed the button...
			$( '#whab-get-location-data' ).click(function (e){
				e.preventDefault();
				clearLB();
				$( '.whab-location-box .whab-loading' ).show();
				$( '.whab-location-box' ).show();

				// Better get crankin' and fetch some data from the Google...
				var whabloc = $( '#whab-location-name' ).val();
				var data = '';
				var url = 'https://maps.googleapis.com/maps/api/geocode/json?language=<?php echo $language; ?>&address=' + whabloc;

				// Ajax request, baby!
				$.get( url, data, function( location_data ) {

					// When Google found something, we go on...
					if ( location_data.status == 'OK' ) {

						// Get the complete address and the geo loctaion
						var formatted_adress;
						var formatted_adress = location_data.results[0]['formatted_address'];
						var geo = location_data.results[0]['geometry']['location'];

						// Set position of map and marker
						map.setCenter( geo );
						marker.setPosition( geo );

						// Store the coordinates
						$.whab.lat = geo['lat'];
						$.whab.long = geo['lng'];

						// Add location to choices, hide spinner
						$( '#geolocation-choices' ).append( '<li><a class="choose-geolocation" href="#choice-0">' + formatted_adress + '</a></li>' );

						$( '.whab-loading' ).hide();

						// Now that the first time worked like a charm, let's ask Google a second time...
						// Construct the url first
						url = 'https://maps.googleapis.com/maps/api/timezone/json?location=' + geo['lat'] + ',' + geo['lng'] + '&timestamp=<?php echo time(); ?>&language=<?php echo $language; ?>';

						// Set timezone
						getTimezoneInfo( url );

					} else {
						// Add error message, because Google clould not find the given location
						$( '#whereabouts-dashboard-widget' ).find( '.whab-location-box' ).after( '<div class="whab-error location"><?php _e( 'Google couldn&#x27;t find the location you entered.', 'whereabouts' ); ?></div>' );
					}
				})
				.done( function(){
					// ...
				}).fail( function(){
					// Ajax request failed
					$( '#whereabouts-dashboard-widget' ).find( '.whab-location-box' ).after( '<div class="whab-error"><?php _e( 'Error. The Google API could not be reached.', 'whereabouts' ); ?></div>' );
				});
			});

			// Put the full location name into the location name input field
			$( document ).on( 'click', '.choose-geolocation', function( e ){
				e.preventDefault();

				// Put location name into its field
				$( '#whab-location-name' ).val( $( this ).text() );

				// Put the coordinates into their hidden field
				$( '#whab-geo' ).val ( $.whab.lat + ', ' + $.whab.long );

				// Put time zone info into their respective fields
				$( '[value="' + $.whab.offset + '"]' ).prop( 'selected', true );
				$( '#whab-timezone-name' ).val( $.whab.timezone );

				// Hide location box
				$( '.whab-location-box' ).animate({
					height: '0px'
				 }, 300, function() {
 					clearLB();
					$( '.whab-location-box' ).css( 'height', 'auto' );
				 });
			});
			
			function clearLB() {
				$( '.whab-location-box' ).hide();
				$( '#geolocation-choices' ).empty();
				$( '.whab-error' ).remove();
			}

			<?php
			/**
			 * Ajax request to get time zone information
			 *
			 * @since 0.6.0
			 *
			 * @param url = 'https://maps.googleapis.com/maps/api/timezone/json?location=LAT,LONG&timestamp=TIMESTAMP&language=LANGUAGE';
			 */
			?>
			function getTimezoneInfo( url ) {

				var data;

				$.get( url, data, function( time_data ) {

					if ( time_data.status == 'OK' ) {

						// Get time zone name and time offset
						var timezone = time_data['timeZoneName'];
						var dstOffset = time_data['dstOffset'];
						var rawOffset = time_data['rawOffset'];

						// Calculate time offset
						var offset = ( rawOffset / 3600 ) + ( dstOffset / 3600 );

						// Set time zone select and time zone name input field
						temp = offset * 3600;
						
						$.whab.offset = temp;
						$.whab.timezone = timezone;

					} else {
						// Add error message, because Google did not return any time zone information
						$( '#whereabouts-dashboard-widget' ).find( '.whab-location-box' ).after( '<div class="whab-error location"><?php _e( 'Google couldn&#x27;t asign a timezone to the location you entered. You need to enter it yourself or try another location.', 'whereabouts' ); ?></div>' );      
					}
				}).done( function() {
					// ...
				})
				.fail( function() {
					// Ajax request failed
					$( '#whereabouts-dashboard-widget' ).find( '.whab-location-box' ).after( '<div class="whab-error"><?php _e( 'Error. The Google API could not be reached.', 'whereabouts' ); ?></div>' );
				});
			}

		});
	</script>

<?php
}