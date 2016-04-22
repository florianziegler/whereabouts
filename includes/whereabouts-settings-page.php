<?php defined( 'ABSPATH' ) OR exit;

/**
 * @package Whereabouts
 * @since 0.1.0
 */


/**
 * Content of the settings page
 * 
 * @since 0.1.0
 */

function whereabouts_load_menu_page() {
    ?>
    <div class="wrap">
        <h2><?php _e( 'Whereabouts Settings', 'whereabouts' ); ?></h2>
        <form class="whab-settings-form" method="post" action="options.php" enctype="multipart/form-data">
        <?php
            settings_fields( 'whab_settings' );
            $options = get_option( 'whab_settings' );
        ?>
            <p><strong><?php _e( 'Select roles that allow users to set their location', 'whereabouts' ); ?>:</strong></p>
            <p>
            <?php

            // Get all possible user roles
            global $wp_roles;
            if ( ! isset( $wp_roles ) ) {
                $wp_roles = new WP_Roles();
            }
            $roles = $wp_roles->get_names();

            // Get allowed user roles from the settings
            if ( isset( $options['allowed_user_roles'] ) && ! empty(  $options['allowed_user_roles'] ) ) { 
                // Get allowed roles
                $allowed_user_roles = $options['allowed_user_roles'];

                // "Check" user roles roles that are allowed
                foreach ($roles as $role_value => $role_name) {
                    if ( in_array( $role_value, $allowed_user_roles ) ) {
                        $checked = ' checked="checked"';
                    }
                    else {
                        $checked = '';
                    }
            		echo '<span class="role"><input type="checkbox" id="' . $role_value . '" name="whab_settings[allowed_user_roles][' . $role_value . ']" value="' . $role_value . '"' . $checked . '><label class="after" for="' . $role_value . '">' . $role_name . '</label></span>';
              	}
            }
            elseif ( isset( $options['allowed_user_roles'] ) AND $options['allowed_user_roles'] === false ) {
                // No roles are checked
                foreach ($roles as $role_value => $role_name) {
            		echo '<span class="role"><input type="checkbox" id="' . $role_value . '" name="whab_settings[allowed_user_roles][' . $role_value . ']" value="' . $role_value . '"><label class="after" for="' . $role_value . '">' . $role_name . '</label></span>';
                }
            }
            else {
                // All roles are checked by default
                foreach ($roles as $role_value => $role_name) {
            		echo '<span class="role"><input type="checkbox" id="' . $role_value . '" name="whab_settings[allowed_user_roles][' . $role_value . ']" value="' . $role_value . '" checked="checked"><label class="after" for="' . $role_value . '">' . $role_name . '</label></span>';
                }
            }
            ?>
            </p>
            <hr />
        <?php
            if ( isset( $options['use_google'] ) && $options['use_google'] == true ) { $checked = ' checked="checked"'; } else { $checked = ''; }
            ?>
            <p><input type="checkbox" id="whab-use-google" name="whab_settings[use_google]" value="1"<?php echo $checked; ?> /> <label class="after" for="whab-use-google"><strong><?php _e( 'Use Google to get location data', 'whereabouts' ); ?></strong></label></p>
            <p><?php _e( 'If you check this box, the Whereabouts plugin will send a request to the Google Geocoding API using the content of the location input field in order to retrieve information about the given location. The time zone will be set for you automatically and you have the option to use the official location name provided by Google.', 'whereabouts' ); ?></p>
            <hr />
            <p><?php _e( 'Set the language in which Google returns the result of your location request', 'whereabouts' ); ?>:</p>
            <p><label for="language"><strong><?php _e( 'API Request Language', 'whereabouts'); ?></strong></label>
                <select name="whab_settings[language]" id="language">
                <?php
                    if ( isset( $options['language'] ) && $options['language'] != '' ) { $selected = $options['language']; } else { $selected = ''; }
                    
                    // Make select box of all supported languages
                    // https://gist.github.com/florianziegler/77cfd2655542cadc47e3
                    // Source: https://developers.google.com/maps/faq#using-google-maps-apis
                    $languages = array(
                        __( 'Select Language', 'whereabouts' ) => '',
                        'العربيّة (ar)' => 'ar',
                        'български (bg)' => 'bg',
                        'Bengali (bn)' => 'bn',
                        'català (ca)' => 'ca',
                        'česky (cs)' => 'cs',
                        'dansk (da)' => 'da',
                        'Deutsch (de)' => 'de',
                        'Ελληνικά (el)' => 'el',
                        'English (en)' => 'en',
                        'English (Australian) (en-AU)' => 'en-AU',
                        'English (Great Britain) (en-GB)' => 'en-GB',
                        'español (es)' => 'es',
                        'Basque (eu)' => 'eu',
                        'فارسی (fa)' => 'fa',
                        'suomi (fi)' => 'fi',
                        'Filipino (fil)' => 'fil',
                        'français (fr)' => 'fr',
                        'Galician (gl)' => 'gl',
                        'Gujarati (gu)' => 'gu',
                        'Hindi (hi)' => 'hi',
                        'Hrvatski (hr)' => 'hr',
                        'Magyar (hu)' => 'hu',
                        'Bahasa Indonesia (id)' => 'id',
                        'italiano (it)' => 'it',
                        'עברית (iw)' => 'iw',
                        '日本語 (ja)' => 'ja',
                        'Kannada (kn)' => 'kn',
                        '한국어 (ko)' => 'ko',
                        'Lietuviškai (lt)' => 'lt',
                        'latviešu (lv)' => 'lv',
                        'Malayalam (ml)' => 'ml',
                        'Marathi (mr)' => 'mr',
                        'Nederlands (nl)' => 'nl',
                        'norsk (no)' => 'no',
                        'Oriya (or)' => 'or',
                        'polski (pl)' => 'pl',
                        'Portuguese (pt)' => 'pt',
                        'Português Brasileiro (pt-BR)' => 'pt-BR',
                        'Português (pt-PT)' => 'pt-PT',
                        'Română (ro)' => 'ro',
                        'Русский (ru)' => 'ru',
                        'slovenský (sk)' => 'sk',
                        'Slovenščina (sl)' => 'sl',
                        'српски (sr)' => 'sr',
                        'svenska (sv)' => 'sv',
                        'Tamil (ta)' => 'ta',
                        'Telugu (te)' => 'te',
                        'ภาษาไทย (th)' => 'th',
                        'Tagalog (tl)' => 'tl',
                        'Türkçe (tr)' => 'tr',
                        'Українська (uk)' => 'uk',
                        'Tiếng Việt (vi)' => 'vi',
                        '简体中文 (zh-CN)' => 'zh-CN',
                        '繁體中文 (zh-TW)' => 'zh-TW',
                    );

                    foreach( $languages as $key => $value ) {
                        echo '<option value="' . $value . '"';
                        if ( $value == $selected ) { echo ' selected="selected"'; }
                        echo '>' . $key . '</option>';
                    }
                    
                ?>
                </select>
            </p>
            <hr />
            <p><?php _e( 'In order to use your Google API Key, you must enable the <strong>Geocoding</strong> and <strong>Time Zone</strong> APIs in the <a href="https://developers.google.com/console/">Google Developer Console</a>. The plugin will send a test request to the api to validate your key.', 'whereabouts' ); ?></p>
            <p><label for="google-maps-api-key"><strong><?php _e ('Google API Key', 'whereabouts' ); ?></strong> (<?php _e( 'optional', 'whereabouts' ); ?>)</label> <input type="text" name="whab_settings[google-maps-api-key]" id="google-maps-api-key" value="<?php if ( isset( $options['google-maps-api-key'] ) && $options['google-maps-api-key'] != '' ) { echo $options['google-maps-api-key']; } ?>" /> <span class="whab-loading"><span>loading</span></span> <input type="submit" class="button secondary" id="whab-validate-key" name="" value="<?php _e( 'Validate API Key', 'whereabouts' ); ?>"></p>
            <input type="hidden" name="whab_settings[key-validation]" id="whab-key-validation" value="<?php if ( isset( $options['google-maps-api-key'] ) && $options['google-maps-api-key'] != '' ) { echo '1'; } else { echo '0'; } ?>" />
            <hr />
            <p><input type="submit" name="save-whab-settings" value="<?php _e( 'Save Settings', 'whereabouts'); ?>" class="button button-primary" /></p>
        </form>
    </div>

<?php 
    add_action( 'admin_footer', 'whereabouts_validate_google_api_key' );
}


/**
 * Validate the Google API Key via an ajax request
 *
 * @since 0.1.0
 */

function whereabouts_validate_google_api_key() { ?>
    <script type="text/javascript" >
        jQuery( document ).ready( function( $ ) {

            // Detect if api key gets changed
            var key = $( '#google-maps-api-key' ).val();
            $( '#google-maps-api-key' ).on( 'input', function(){
                if ( key == $( '#google-maps-api-key' ).val() ) {
                    $( '#whab-key-validation' ).val( '1' );
                }
                else {
                    $( '#whab-key-validation' ).val( '0' );
                }
            });

            // Start validation process
            $( '#whab-validate-key' ).click( function( e ) {
                e.preventDefault();

                key = $( '#google-maps-api-key' ).val();

                if ( key != '' ) {

                    // Remove messages - if any are shown
                    $( '.whab-invalid, .whab-valid' ).remove();
                    $( '.error' ).remove();

                    // Show spinner
                    $( '.whab-loading' ).show();

                    // Make urls
                    url1 = 'https://maps.googleapis.com/maps/api/geocode/json?language=en&address=Edinburgh&key=' + key;
                    url2 = 'https://maps.googleapis.com/maps/api/timezone/json?timestamp=1000&language=en&location=55.953252,-3.188267&key=' + key;

                    var data = '';

                    $.get( url1, data, function( result ) {

                        // Hide the spinner
                        $( '.whab-loading' ).hide();
                        // Set key validation to false
                        $( '#whab-key-validation' ).val( '0' );

                        if ( result.status == 'OK' ) {
                        
                            $.get( url2, data, function( result2 ) {

                                if ( result2.status == 'OK' ) {  
                                    // Set key validation to true
                                    $( '#whab-key-validation' ).val( '1' );
                                    // Show checkmark
                                    $( '<span class="whab-valid"><span><?php _e( 'You key is valid.', 'whereabouts' ); ?></span></span>' ).insertAfter( '#google-maps-api-key' );
                                }
                                else {
                                    // Show x
                                    $( '<span class="whab-invalid"><span><?php _e( 'Your key is not valid.', 'whereabouts' ); ?></span></span>' ).insertAfter( '#google-maps-api-key' );
                                }
                            });
                        }
                        else {
                            // Show x
                            $( '<span class="whab-invalid"><span><?php _e( 'Your key is not valid.', 'whereabouts' ); ?></span></span>' ).insertAfter( '#google-maps-api-key' );
                        }
                    }).fail( function(){
                        // Show error message
                        $( '<div class="error settings-error"><p><strong><?php _e( 'Validation server could not be reached. Try again later.', 'whereabouts' ); ?></strong></p></div>' ).insertAfter( '.wrap h2' );
                    });
                }
            });
        });
    </script>

<?php 
}