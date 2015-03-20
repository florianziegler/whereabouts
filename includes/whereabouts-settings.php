<?php defined( 'ABSPATH' ) OR exit;

/**
 * @package whereabouts
 * @since 0.1.0
 */


/**
 * Register settings
 *
 * @since 0.1.0
 */

add_action( 'admin_init', 'whereabouts_register_settings' );

function whereabouts_register_settings() {
    // Plugin settings
    register_setting( 'whab_settings', 'whab_settings', 'whereabouts_settings_validate' );
}


/**
 * Validate Whereabouts location
 *
 * @since 0.1.0
 */

function whereabouts_validate_save_location( $args ) {

    if ( ! isset( $args['location_name'] ) OR empty( $args['location_name'] ) ) {
        // Add settings error if location field is empty
        $error = __( 'Please enter a location.', 'whereabouts' );
    } else {
        // Sanitize location name before saving
        $args['location_name'] = sanitize_text_field( $args['location_name'] );
    }

    // Sanitize time difference before saving    
    $args['utc_difference'] = htmlentities( $args['utc_difference'], ENT_NOQUOTES, 'UTF-8' );

    // Sanitize time zone name before saving
    $args['timezone_name'] = sanitize_text_field( $args['timezone_name'] );

    // Sanitize geo data before saving
    $args['geo'] = sanitize_text_field( $args['geo'] );

    // If there was no error, save location info to the user meta and display a message
    if ( ! isset( $error ) ) {

        // Get current user id
        $current_user = wp_get_current_user();

        if ( !( $current_user instanceof WP_User ) ) { return; }
        $options = get_user_meta( $current_user->ID, 'whab_location_data', true );

        // Check if location field is already in there
        $location = get_user_meta( $current_user->ID, 'whab_location_data', true );

        // If location is given, do the update
        if ( isset( $location ) AND ! empty( $location ) ) {
            update_user_meta( $current_user->ID, 'whab_location_data', $args );
        }
        // If user has not saved a location yet, create the meta
        else {
            add_user_meta( $current_user->ID, 'whab_location_data', $args, true );
        }

        // Tell the user, that it worked!
        echo '<div class="whab-message whab-success">' . __( 'Location saved.', 'whereabouts') . '</div>';
    }
    else {
        // Display error
        echo '<div class="whab-message whab-error">' . $error . '</div>';
    }

}


/**
 * Validate Whereabouts settings
 *
 * @since 0.1.0
 */

function whereabouts_settings_validate( $args ) {

    // Sanitize allowed roles
    if ( isset( $args['allowed_user_roles'] ) AND is_array( $args['allowed_user_roles'] ) ) {
        // Get all possibble roles
        global $wp_roles;
        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
        $possible_roles = $wp_roles->get_names();
        // We only need the "slug"
        $possible_roles = array_keys( $possible_roles );

        // We keep those roles, that exist in both arrays
        $args['allowed_user_roles'] = array_intersect( $args['allowed_user_roles'], $possible_roles );
    }
    else {
        $args['allowed_user_roles'] = false;
    }

    // use-google can either be true or false... make sure it is either one of them
    if ( ! empty( $args['use_google'] ) ) {
        if ( $args['use_google'] != true ) {
            $args['use_google'] = false;
        }
    }

    // Check if the chosen language is allowed...
    if ( ! empty( $args['language'] ) ) {

        $allowed_languages = array( 'ar', 'eu', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'en', 'en-AU', 'en-GB', 'es', 'eu', 'fa', 'fi', 'fil', 'fr', 'gl', 'gu', 'hi', 'hr', 'hu', 'id', 'it', 'iw', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'nl', 'nn', 'no', 'or', 'pl', 'pt', 'pt-BR', 'pt-PT', 'rm', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'tl', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh-CN', 'zh-TW' );

        if ( ! in_array( $args['language'] , $allowed_languages ) ) {
            add_settings_error( 'language', 'whab-language', __( 'This language is not supported.', 'whereabouts' ), $type = 'error' );
            $args['language'] = '';
        }
    }

    // Check if the Google API key has been validated
    if ( ! empty( $args['google-maps-api-key'] ) ) {

        if ( ! isset( $args['key-validation'] ) OR $args['key-validation'] != true ) {
            add_settings_error( 'api-key', 'whab-api-key', __( 'The key you provided is not valid.', 'whereabouts' ), $type = 'error' );
            unset( $args['google-maps-api-key'] );
        } else {
            $args['google-maps-api-key'] = sanitize_text_field( $args['google-maps-api-key'] );
        }

        // We don't need to save that
        unset( $args['key-validation'] );                         
    }

    return $args;

}