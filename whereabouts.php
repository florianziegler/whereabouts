<?php

defined( 'ABSPATH' ) OR exit;
/*
Plugin Name: Whereabouts
Plugin URI: http://florianziegler.de/whereabouts
Description: Show visitors your current location in the world and the corresponding time (zone). Enable Google API support in the <a href="options-general.php?page=whereabouts">Settings</a>.
Version: 0.4.0
Author: Florian Ziegler
Author URI: http://florianziegler.de/
License: GPLv2 or later
Text-Domain: whereabouts
*/


/**
 * Plugin Setup
 *
 * @since 0.1.0
 */

add_action( 'after_setup_theme', 'whereabouts_setup' );

if ( ! function_exists( 'whereabouts_setup' ) ) {

	function whereabouts_setup() {

		// Define include path for this plugin
		define( 'WHEREABOUTS_PATH', plugin_dir_path( __FILE__ ) );
        
		// Define url for this plugin
		define( 'WHEREABOUTS_URL', plugin_dir_url( __FILE__ ) );

		// Custom menu page
		require WHEREABOUTS_PATH . 'includes/whereabouts-settings.php';
        
		// Include function to load the custom menu page
		require WHEREABOUTS_PATH . 'includes/whereabouts-settings-page.php';

		// Include function to display Whereabouts as a widget on the Dashboard
		require WHEREABOUTS_PATH . 'includes/whereabouts-dashboard.php';
        
		// Include function to display Whereabouts as a widget on the Dashboard
		require WHEREABOUTS_PATH . 'includes/whereabouts-widget.php';
        
		// Include function to display Whereabouts on the site
		require WHEREABOUTS_PATH . 'includes/whereabouts-display.php';

	}

}


/**
 * Load language files
 *
 * @since 0.1.0
 */

load_plugin_textdomain( 'whereabouts', false, basename( dirname( __FILE__ ) ) . '/languages' );


/**
 * Enqueue styles and scripts
 *
 * @since 0.1.0
 */

function whereabouts_init() {
	if ( is_admin() ) {
        wp_enqueue_style( 'whereabouts-admin', WHEREABOUTS_URL . '/css/whereabouts-admin.css', array( 'dashicons') );           
    }
}
add_action( 'init', 'whereabouts_init' );


/**
 * Register settings menu for Whereabouts
 *
 * @since 0.1.0
 */

add_action( 'admin_menu', 'whereabouts_menu' );

function whereabouts_menu() {
	add_options_page(
		'Whereabouts',
		'Whereabouts',
		'manage_options',
		'whereabouts',
		'whereabouts_load_menu_page'
	);

}


/**
 * Delete options when plugin is deleted
 *
 * @since 0.1.0
 */

register_uninstall_hook( __FILE__, 'uninstall_whereabouts' );

function uninstall_whereabouts() {
    delete_option( 'whab_settings' );
    delete_option( 'whab_location_data' );
    unregister_widget( 'Whereabouts_Widget' );
}


/**
 * Update: Move location data into user meta
 *
 * @since 0.4.0
 */

function whereabouts_post_upgrade() {

    // Check if location is stored in settings.
    // If so: move it to current user data.
    $old_location = get_option( 'whab_location_data' );
    
    $current_user = wp_get_current_user();
    if ( ! ( $current_user instanceof WP_User ) ) { return; }
    $user_location = get_user_meta( $current_user->ID, 'whab_location_data', true );
    
    if ( $user_location AND ! empty( $user_location ) ) {
        // Do nothing...
    }
    else {
        add_user_meta( $current_user->ID, 'whab_location_data', $old_location, true );
        delete_option( 'whab_location_data' );
    }
}

add_action( 'upgrader_process_complete', 'whereabouts_post_upgrade' );