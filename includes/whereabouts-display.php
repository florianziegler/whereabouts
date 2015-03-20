<?php defined( 'ABSPATH' ) OR exit;

/** 
 * @package Whereabouts
 * @since 0.1.0
 */


/**
 * Display location as a widget
 *
 * @since 0.1.0
 */

function whereabouts_display_location( $args ) {
    
    // If use standard if no time format is specified
    if ( empty( $args['time_format'] ) ) {
        $args['time_format'] = 'H:i';
    }

    $user_exists = false;

    if ( isset ( $args['user'] ) ) {
        // Check if user exists...
        $user_exists = get_user_by( 'id', $args['user'] );

        // ...and has location data.
        $location = get_user_meta( $args['user'], 'whab_location_data', true );
    }

    if ( $user_exists AND ! empty( $location ) ) {

        $output = '<dl class="whab-info">
                     <dt class="whab-label whab-label-location">' . __( 'Current Location:', 'whereabouts' ) . '</dt>
                     <dd class="whab-location">';
                 
        if ( isset( $args['link_location'] ) AND $args['link_location'] == true ) {
            $output .= '<a title="Show location on Google Maps" href="https://www.google.co.uk/maps/place/' . str_replace( ' ', '', $location['location_name'] ) . '">';
        }
        $output .= $location['location_name'];
        if ( isset( $args['link_location'] ) AND $args['link_location'] == true ) {    
            $output .= '</a>';
        }
        $output .= '</dd>
                     <dt class="whab-label whab-label-time">' . __( 'Local Time:', 'whereabouts' ) . '</dt>';

        $offset = $location['utc_difference'];
        $timezone_name = $location['timezone_name'];

        $output .= '<dd class="whab-time">';

        $current_time = time();
        $current_time = date( $args['time_format'], $current_time + $offset );

        $output .= $current_time;
 
        if ( isset( $args['show_tz'] ) AND $args['show_tz'] == true AND !empty( $timezone_name ) ) {
            $output .= ' <span class="whab-timezone-name"> (' . $timezone_name . ')</span>';
        }

        $output .= '</dd></dl>';

        // Add filter for widget output, give the dev all he/she needs to completely (re)build the widget
        $output = apply_filters( 'whab_widget_output', $output, $args, $location );

        return $output;

    }
    else {
        // User either doesn't exist or has no location info
        return;
    }
}


/**
 * Display location via shortcode
 *
 * @since 0.5.0
 */

add_shortcode( 'whereabouts', 'whereabouts_display_location' );