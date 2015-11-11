<?php

    /*
     * Function that returns a front-end logout message from the wppb-logout shortcode
     *
     * @param $atts     The shortcode attributes
     */
    function wppb_front_end_logout( $atts ) {

        if( !is_user_logged_in() )
            return;

        $current_user = get_userdata( get_current_user_id() );

        extract( shortcode_atts( array( 'text' => sprintf( __('You are currently logged in as %s. ','profile-builder') ,$current_user->user_login) , 'redirect' => wppb_curpageurl(), 'redirect_priority' => 'normal', 'link_text' => __('Log out &raquo;','profile-builder')), $atts ) );

		if( PROFILE_BUILDER == 'Profile Builder Pro' ) {
			$wppb_module_settings = get_option( 'wppb_module_settings' );

			if( isset( $wppb_module_settings['wppb_customRedirect'] ) && $wppb_module_settings['wppb_customRedirect'] == 'show' && $redirect_priority != 'top' && function_exists( 'wppb_custom_redirect_url' ) ) {
				$redirect = wppb_custom_redirect_url( 'after_logout', $redirect );
			}
		}
		$redirect = apply_filters( 'wppb_after_logout_redirect_url', $redirect );

        $logout_link = '<a href="' . wp_logout_url( $redirect ) . '" class="wppb-logout-url" title="' . __( 'Log out of this account', 'profile-builder' ) . '">' . $link_text . '</a>';

        $meta_tags = apply_filters( 'wppb_front_end_logout_meta_tags', array( '{{meta_user_name}}', '{{meta_first_name}}', '{{meta_last_name}}', '{{meta_display_name}}' ) );
        $meta_tags_values = apply_filters( 'wppb_front_end_logout_meta_tags_values', array( $current_user->user_login, $current_user->first_name, $current_user->last_name, $current_user->display_name ) );

        $text = apply_filters( 'wppb_front_end_logout_text', str_replace( $meta_tags, $meta_tags_values, $text ), $current_user );

        return '<p class="wppb-front-end-logout"><span>' . $text . '</span>' . $logout_link . '</p>';
    }