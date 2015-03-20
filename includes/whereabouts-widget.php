<?php defined( 'ABSPATH' ) OR exit;

/**
 * @package Whereabouts
 * @since 0.1.0
 */


/** 
 * Tell WordPress that there is a new widget in town!
 *
 * @since 0.1.0
 */

class Whereabouts_Widget extends WP_Widget {

    // Instantiate parent object
	function Whereabouts_Widget() {

        $widget_slug = 'whereabouts_user_widget';

		parent::__construct( $widget_slug, 'Whereabouts', array( 'description' => __( 'Shows current location and timezone.', 'whereabouts' ) ) );
	}

    // Front end display of widget
	function widget( $args, $instance ) {

        if ( isset ( $instance['user'] ) AND !empty( $instance['user'] ) ) {
            $user = $instance['user'];
        }
        else {
            $user = false;
        }

        $title = apply_filters( 'widget_title', $instance['title'] );
        $link_location = apply_filters( 'widget_title', $instance['link_location'] );
        $show_tz = apply_filters( 'widget_title', $instance['show_tz'] );
        $time_format = apply_filters( 'widget_title', $instance['time_format'] );

        // Set standard time format, if none is set
        if ( empty( $time_format ) ) {
            $time_format = 'H:i';
        }

        // Echo widget
        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        $input = array( 'link_location' => $link_location, 'show_tz' => $show_tz, 'time_format' => $time_format, 'user' => $user );
        echo whereabouts_display_location( $input );
        echo $args['after_widget'];

        
	}

	// Save widget options    
	function update( $new_instance, $old_instance ) {

        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['user'] = ( ! empty( $new_instance['user'] ) ) ? strip_tags( $new_instance['user'] ) : '';
        $instance['link_location'] = ( ! empty( $new_instance['link_location'] ) ) ? strip_tags( $new_instance['link_location'] ) : '';
        $instance['show_tz'] = ( ! empty( $new_instance['show_tz'] ) ) ? strip_tags( $new_instance['show_tz'] ) : '';
        $instance['time_format'] = ( ! empty( $new_instance['time_format'] ) ) ? strip_tags( $new_instance['time_format'] ) : 'H:i';
        return $instance;

	}

    // Output admin widget options form
	function form( $instance ) {

        $settings = get_option( 'whab_settings' );

        // Set title variable, if it is not saved
        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        }
        else {
            $title = '';
        }
        // Set user variable, if it is not saved
        if ( isset( $instance['user'] ) ) {
            $user = $instance['user'];
        }
        else {
            $user = '';
        }

        // Display warning if user has not his/her location yet.
        if ( $user AND !empty( $user ) ) {
            $location_exists = get_user_meta( $user, 'whab_location_data', true );
            if ( ! $location_exists ) {
                $user_data = get_userdata( $user );
                echo '<p><strong style="color: #c00;">' . sprintf( __('This widget won\'t be displayed until %s saved his/her location.', 'whereabouts'), $user_data->display_name ) . '</strong></p>';
            }
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'whereabouts' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
            // Add user select box, do choose which user should be displayed in the widget
            echo '<p class="whab_widget_user_select"><label for="user">' . __( 'Show location of this user:', 'whereabouts' ) . '</label>';

            if ( $user AND !empty( $user ) ) {
                wp_dropdown_users( array( 'name' => $this->get_field_name( 'user' ), 'selected' => $user ) );
            }
            else {
                wp_dropdown_users( array( 'name' => $this->get_field_name( 'user' ) ) );                
            }
            echo '</p>';
        ?>
        <p>
            <input id="<?php echo $this->get_field_id( 'link_location' ); ?>" name="<?php echo $this->get_field_name( 'link_location' ); ?>" type="checkbox" value="1"<?php if ( isset( $instance['link_location'] ) AND $instance['link_location'] == true ) { echo 'checked="checked"'; } ?> />
            <label for="<?php echo $this->get_field_id( 'link_location' ); ?>"><?php _e( 'Link location to Google Maps', 'whereabouts' ); ?></label> 
        </p>
        <p>
            <input id="<?php echo $this->get_field_id( 'show_tz' ); ?>" name="<?php echo $this->get_field_name( 'show_tz' ); ?>" type="checkbox" value="1"<?php if ( isset( $instance['show_tz'] ) AND $instance['show_tz'] == true ) { echo 'checked="checked"'; } ?> />
            <label for="<?php echo $this->get_field_id( 'show_tz' ); ?>"><?php _e( 'Show time zone name', 'whereabouts' ); ?></label> 
        </p>
        <p>
        	<strong><?php _e( 'Time Format', 'whereabouts' ); ?></strong><br />
            <span class="whab-pair"><input type="radio" name="<?php echo $this->get_field_name( 'time_format' ); ?>" id="<?php echo $this->get_field_id( 'time_format_H_i' ); ?>" value="H:i"<?php if ( ! isset( $instance['time_format'] ) OR ( isset( $instance['time_format'] ) AND $instance['time_format'] == 'H:i' ) ) { echo 'checked="checked"'; } ?> /> <label for="<?php echo $this->get_field_id( 'time_format_H_i' ); ?>"><?php echo date( 'H:i' ); ?></label></span>
            <span class="whab-pair"><input type="radio" name="<?php echo $this->get_field_name( 'time_format' ); ?>" id="<?php echo $this->get_field_id( 'time_format_g_i_a' ); ?>" value="g:i a"<?php if ( isset( $instance['time_format'] ) AND $instance['time_format'] == 'g:i a' ) { echo 'checked="checked"'; } ?> /> <label for="<?php echo $this->get_field_id( 'time_format_g_i_a' ); ?>"><?php echo date( 'g:i a' ); ?></label></span>
        	<span class="whab-pair"><input type="radio" name="<?php echo $this->get_field_name( 'time_format' ); ?>" id="<?php echo $this->get_field_id( 'time_format_g_i_A' ); ?>" value="g:i A"<?php if ( isset( $instance['time_format'] ) AND $instance['time_format'] == 'g:i A' ) { echo 'checked="checked"'; } ?> /> <label for="<?php echo $this->get_field_id( 'time_format_g_i_A' ); ?>"><?php echo date( 'g:i A' ); ?></label></span>
        </p>
        <?php
	}
}


/** 
 * Register the widget
 *
 * @since 0.1.0
 */

add_action( 'widgets_init', 'whereabouts_register_widgets' );

function whereabouts_register_widgets() {
	register_widget( 'Whereabouts_Widget' );
}