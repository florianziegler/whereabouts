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

function whereabouts_build_dashboard_widget() { ?>

    <?php
    // Validate & save $_POST data, when new location info was submitted.
    if ( isset( $_REQUEST['whab_location_data'] ) ) {
        $args = $_REQUEST['whab_location_data'];
        whereabouts_validate_save_location( $args );
    }
    
    ?>

    <form method="post" action="">

    <?php
        // Get a nonce field for verifying purposes.
        wp_nonce_field();

        // Load plugin settings
        $settings = get_option( 'whab_settings' );

        // Load location data from the current user
        $current_user = wp_get_current_user();
        if ( !( $current_user instanceof WP_User ) ) { return; }
        $location = get_user_meta( $current_user->ID, 'whab_location_data', true );

        // If the plugin is set to use Google, insert maps api script and map canvas
        if ( isset( $settings['use_google'] ) AND $settings['use_google'] == true ) { ?>
            <script src="//maps.googleapis.com/maps/api/js?v=3.exp"></script>
            <div id="whab-map"></div>     
    <?php }
        
        //  Add shortcut to the Whereabouts settings ?>
        <a class="whab-settings" href="options-general.php?page=whereabouts" title="<?php _e( 'Go to settings', 'whereabouts' ); ?>"><span><?php _e( 'Settings', 'whereabouts' ); ?></span></a>

    <?php // If Whereabouts is *not* set to use Google, a message is displayed, that says why it would be a good idea to use this feature
        if ( ! isset( $settings['use_google'] ) OR $settings['use_google'] != true ) { ?>
            <div class="whab-message"><?php _e( 'Activate automatic time zone setting via Google in the <a href="options-general.php?page=whereabouts">settings</a>. As a bonus, you will also see a map of your current location.', 'whereabouts' ); ?></div>
    <?php }
            
        // Add location input field ?>
        <p class="whab-location-input-wrapper"><label for="whab-location-name"><?php _e( 'Enter your current location', 'whereabouts' ); ?></label>
            <input type="text" name="whab_location_data[location_name]" id="whab-location-name" value="<?php if ( isset( $location['location_name'] ) && $location['location_name'] != '' ) { echo $location['location_name']; } else { echo ''; } ?>" />
        
    <?php // If enabled, add 'get location data' button. Also: add the info box, that shows the full location name
        if ( isset( $settings['use_google'] ) AND $settings['use_google'] == true ) { ?>
            <input type="submit" class="button button-secondary" name="get-location-data" id="whab-get-location-data" value="<?php _e( 'Get location data', 'whereabouts' ); ?>" /></p>

            <div class="whab-location-box">
                <div class="whab-location-info">
                    <p><a id="whab-use-location-name" class="dashicons dashicons-redo" href="#" title="<?php _e( 'Use Location name', 'whereabouts' ); ?>"><span><?php _e( 'Use Location Name', 'whereabouts' ); ?></span></a><span id="whab-temp-location-name"></span></p>
                <?php // If not set, diplay a hint, that the request language can be adjusted in the plugin's settings
                    if ( ! isset( $settings['language'] ) OR $settings['language'] == '' ) { 
                        _e( '<p class="whab-language-settings-hint"><strong>Tip:</strong> You can set the language in which Google returns this information in the <a href="options-general.php?page=whereabouts">settings</a>.</a>', 'whereabouts' );
                    } ?>
                </div>
            </div>

    <?php }
    
        // Add time zone select box ?>
        <p class="whab-col-25"><label for="whab-timezone-select"><?php _e( 'Timezone', 'whereabouts'); ?></label>
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
        <p class="whab-col-75"><label for="whab-timezone-name"><?php _e( 'Timezone Name', 'whereabouts' ); ?></label> <input type="text" name="whab_location_data[timezone_name]" id="whab-timezone-name" value="<?php if ( isset( $location['timezone_name'] ) && $location['timezone_name'] != '' ) { echo $location['timezone_name']; } else { echo ''; } ?>" /></p>

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

    $current_user = wp_get_current_user();
    if ( ! ( $current_user instanceof WP_User ) ) { return; }
    $options = get_user_meta( $current_user->ID, 'whab_location_data', true );
    
    // Load language form the settings, use english if not set
    $settings = get_option( 'whab_settings' );
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

            // Initialize the map. Load geo data, if not set - use Edinburgh...
            function initialize() {
                var myLatlng = new google.maps.LatLng( <?php if ( isset( $options['geo'] ) && $options['geo'] != '' ) { echo $options['geo']; } else { echo '55.953252, -3.188267'; } ?> );
                var mapOptions = {
                    zoom: 4,
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
            }
            google.maps.event.addDomListener( window, 'load', initialize );

            // Oh snap. Someone pushed the button...
            $( '#whab-get-location-data' ).click(function (e){
                e.preventDefault();

                // Remove error message - if one was created earlier, and hide location-box
                $( '.whab-error' ).remove();
                $( '.whab-location-box' ).hide();
            
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

                        // Put the geo value into the hidden options field
                        $( '#whab-geo' ).val ( geo['lat'] + ', ' + geo['lng'] );

                        // Now that the first time worked like a charm, let's ask Google a second time...
                        // Construct the url first
                        url = 'https://maps.googleapis.com/maps/api/timezone/json?location=' + geo['lat'] + ',' + geo['lng'] + '&timestamp=<?php echo time(); ?>&language=<?php echo $language; ?>';

                        // Ajax request - Part II, baby!
                        $.get( url, data, function( time_data ) {

                            if ( time_data.status == 'OK' ) {

                                // Get time zone name and time offset
                                var timezone = time_data['timeZoneName'];
                                var dstOffset = time_data['dstOffset'];
                                var rawOffset = time_data['rawOffset'];

                                // Calculate time offset
                                var offset = ( rawOffset / 3600 ) + ( dstOffset / 3600 );

                                // Put full location name into the location box, then show the location box
                                $( '#whab-temp-location-name' ).html( formatted_adress );
                                $( '.whab-location-box' ).show();

                                // Set time zone select and time zone name input field
                                temp = offset * 3600;
                                $( '[value="' + temp + '"]' ).prop( 'selected', true );
                                $( '#whab-timezone-name' ).val( timezone );

                            } else {
                                // Add error message, because Google did not return any time zone information
                                $( '#whereabouts-dashboard-widget' ).find( '.whab-location-box' ).after( '<div class="whab-error location"><?php _e( 'Google couldn&#x27;t asign a timezone to the location you entered. You need to enter it yourself or try another location.', 'whereabouts' ); ?></div>' );      
                            }
                        }).done( function() {
                            // Second ajax reqeust went fine...
                        })
                        .fail( function() {
                            // Second ajax request failed
                            $( '#whereabouts-dashboard-widget' ).find( '.whab-location-box' ).after( '<div class="whab-error"><?php _e( 'Error. The Google API could not be reached.', 'whereabouts' ); ?></div>' );
                        });

                    } else {
                        // Add error message, because Google clould not find the given location
                        $( '#whereabouts-dashboard-widget' ).find( '.whab-location-box' ).after( '<div class="whab-error location"><?php _e( 'Google couldn&#x27;t find the location you entered.', 'whereabouts' ); ?></div>' );
                    }
                })
                .done( function(){
                    // First ajax reqeust went fine...
                }).fail( function(){
                    // First ajax request failed
                    $( '#whereabouts-dashboard-widget' ).find( '.whab-location-box' ).after( '<div class="whab-error"><?php _e( 'Error. The Google API could not be reached.', 'whereabouts' ); ?></div>' );
                });
            });

            // Put the full location name into the location name input field
            $( '#whab-use-location-name' ).click(function( e ){
                e.preventDefault();
                $( '#whab-location-name' ).val( $( '#whab-temp-location-name' ).text() );
            });

        });
    </script>

<?php
}
?>