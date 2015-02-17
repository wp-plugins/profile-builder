<?php
/**
 * Function that changes the auto generated password with the one selected by the user.
 */
function signup_password_random_password_filter( $password ) {
	global $wpdb;

	$key = ( !empty( $_GET['key'] ) ? $_GET['key'] : null );
	$key = ( !empty( $_POST['key'] ) ? $_POST['key'] : null );

	if ( !empty( $_POST['user_pass'] ) )
		$password = $_POST['user_pass'];
		
	elseif ( !is_null( $key ) ) {
		$signup = ( is_multisite() ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->signups . " WHERE activation_key = %s", $key ) ) : $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->base_prefix . "signups WHERE activation_key = %s", $key ) ) );
		
		if ( empty( $signup ) || $signup->active ) {
			//bad key or already active
		} else {
			//check for password in signup meta
			$meta = unserialize( $signup->meta );
			
			$password = $meta['user_pass'];
		}
	}
	
	return apply_filters( 'wppb_generated_random_password', $password, $key );
}
add_filter( 'random_password', 'signup_password_random_password_filter' );

/**
 * Activate a signup.
 *
 *
 * @param string $key The activation key provided to the user.
 * @return array An array containing information about the activated user and/or blog
 */
function wppb_activate_signup( $key ) {
	global $wpdb;
	$bloginfo = get_bloginfo( 'name' );
	$wppb_general_settings = get_option( 'wppb_general_settings' );

	$signup = ( is_multisite() ? $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE activation_key = %s", $key) ) : $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->base_prefix."signups WHERE activation_key = %s", $key ) ) );
	
	if ( empty( $signup ) )
		return apply_filters( 'wppb_register_activate_user_error_message1', '<p class="error">'.__( 'Invalid activation key!', 'profilebuilder' ).'</p>');

	if ( $signup->active )
		if ( empty( $signup->domain ) )
			return apply_filters( 'wppb_register_activate_user_error_message2', '<p class="error">'.__( 'This username is now active!', 'profilebuilder' ).'</p>' );

	$meta = unserialize( $signup->meta );
	
	$user_login = ( ( isset( $wppb_general_settings['loginWith'] ) && ( $wppb_general_settings['loginWith'] == 'email' ) ) ? trim( $signup->user_email ) : trim( $signup->user_login ) );
		
	$user_email = esc_sql( $signup->user_email );
    /* the password is in hashed form in the signup table so we will add it later */
	$password = NULL;

	$user_id = username_exists( $user_login );

	if ( !$user_id )
		$user_id = wppb_create_user( $user_login, $password, $user_email );
	else
		$user_already_exists = true;

	if ( ! $user_id )
		return apply_filters( 'wppb_register_activate_user_error_message4', '<p class="error">'.__('Could not create user!', 'profilebuilder').'</p>' );
		
	elseif ( isset( $user_already_exists ) && ( $user_already_exists == true ) )
		return apply_filters( 'wppb_register_activate_user_error_message5', '<p class="error">'.__( 'This username is already activated!', 'profilebuilder' ).'</p>' );
	
	else{
		$inserted_user = ( is_multisite() ? $wpdb->update( $wpdb->signups, array( 'active' => 1, 'activated' => current_time( 'mysql', true ) ), array( 'activation_key' => $key ) ) : $wpdb->update( $wpdb->base_prefix.'signups', array( 'active' => 1, 'activated' => current_time( 'mysql', true ) ), array( 'activation_key' => $key ) ) );

		wppb_add_meta_to_user_on_activation( $user_id, '', $meta );
		
		// if admin approval is activated, then block the user untill he gets approved
		$wppb_generalSettings = get_option('wppb_general_settings');
		if ( isset( $wppb_generalSettings['adminApproval'] ) && ( $wppb_generalSettings['adminApproval'] == 'yes' ) ){
			wp_set_object_terms( $user_id, array( 'unapproved' ), 'user_status', false );
			clean_object_term_cache( $user_id, 'user_status' );
		}

        if ( !isset( $wppb_generalSettings['adminApproval'] ) )
            $wppb_generalSettings['adminApproval'] = 'no';

        /* copy the hashed password from signup meta to wp user table */
        if( !empty( $meta['user_pass'] ) ){
            /* we might still have the base64 encoded password in signups and not the hash */
            if( base64_encode(base64_decode($meta['user_pass'], true)) === $meta['user_pass'] )
                $meta['user_pass'] = wp_hash_password( $meta['user_pass'] );

            $wpdb->update( $wpdb->users, array('user_pass' => $meta['user_pass'] ), array('ID' => $user_id) );
        }
		
		wppb_notify_user_registration_email($bloginfo, $user_login, $user_email, 'sending', $password, $wppb_generalSettings['adminApproval']);
		
		do_action( 'wppb_activate_user', $user_id, $password, $meta );
		
		if ( $inserted_user ){
			$success_message = apply_filters('wppb_success_email_confirmation', '<p class="wppb-success">'. __('Your email was successfully confirmed.', 'profilebuilder') .'</p><!-- .success -->');
            $admin_approval_message = apply_filters('wppb_email_confirmation_with_admin_approval', '<p class="alert">'. __('Before you can access your account, an administrator needs to approve it. You will be notified via email.', 'profilebuilder') . '</p>' );

            $wppb_general_settings = get_option( 'wppb_general_settings', 'false' );
            if ( !empty( $wppb_general_settings['adminApproval'] ) && $wppb_general_settings['adminApproval'] == 'yes' ){
                return $success_message . $admin_approval_message;
            } else {
                return $success_message;
            }
        } else {
			return apply_filters('wppb_register_failed_user_activation', '<p class="error">'. __('There was an error while trying to activate the user.', 'profilebuilder') .'</p><!-- .error -->');
        }
	}		
}

//function to display the registration page
function wppb_front_end_register( $atts ){
	extract( shortcode_atts( array( 'role' => get_option( 'default_role' ), 'form_name' => 'unspecified', 'redirect_url' => '' ), $atts, 'wppb-register' ) );
	global $$form_name;

    $$form_name = new Profile_Builder_Form_Creator( array( 'form_type' => 'register', 'form_name' => $form_name, 'role' => ( is_object( get_role( $role ) ) ? $role : get_option( 'default_role' ) ) , 'redirect_url' => $redirect_url ) );

    return $$form_name;
}

// function to choose whether to display the registration page or the validation message
function wppb_front_end_register_handler( $atts ){

	return ( isset( $_GET['activation_key'] ) ? wppb_activate_signup ( $_GET['activation_key'] ) : wppb_front_end_register( $atts ) );
}