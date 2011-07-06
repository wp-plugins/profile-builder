<?php if (!defined('ProfileBuilderVersion')) exit('No direct script access allowed');
/*
Original Plugin Name: OptionTree
Original Plugin URI: http://wp.envato.com
Original Author: Derek Herman
Original Author URI: http://valendesigns.com
*/

/**
 * Functions Load
 *
 */
 /* whitelist options, you can add more register_settings changing the second parameter */
 
 function wppb_register_settings() { 								
	register_setting( 'wppb_option_group', 'wppb_default_settings' );
	register_setting( 'wppb_default_style', 'wppb_default_style' );
	register_setting( 'wppb_display_admin_settings', 'wppb_display_admin_settings' );
	register_setting( 'wppb_profile_builder_pro_serial', 'wppb_profile_builder_pro_serial' );
}


function wppb_add_plugin_stylesheet() {
		$wppb_showDefaultCss = get_option('wppb_default_style');
        $styleUrl_default = wppb_plugin_url . '/assets/css/front.end.css';
        $styleUrl_white = wppb_plugin_url . '/premium/assets/css/front.end.white.css';
        $styleUrl_black = wppb_plugin_url . '/premium/assets/css/front.end.black.css';
        $styleFile_default = wppb_plugin_dir . '/assets/css/front.end.css';
        $styleFile_white = wppb_plugin_dir . '/premium/assets/css/front.end.white.css';
        $styleFile_black = wppb_plugin_dir . '/premium/assets/css/front.end.black.css';
        if ( (file_exists($styleFile_default)) && ($wppb_showDefaultCss == 'yes') ) {
            wp_register_style('wppb_stylesheet', $styleUrl_default);
            wp_enqueue_style( 'wppb_stylesheet');
        }elseif ( (file_exists($styleFile_white)) && ($wppb_showDefaultCss == 'white') ) {
            wp_register_style('wppb_stylesheet', $styleUrl_white);
            wp_enqueue_style( 'wppb_stylesheet');
        }elseif ( (file_exists($styleFile_black)) && ($wppb_showDefaultCss == 'black') ) {
            wp_register_style('wppb_stylesheet', $styleUrl_black);
            wp_enqueue_style( 'wppb_stylesheet');
        }
}


function wppb_show_admin_bar($content){
	global $current_user;
	$admintSettingsPresent = get_option('wppb_display_admin_settings','not_found');
	if ($admintSettingsPresent != 'not_found'){    
		$wppb_showAdminBar = get_option('wppb_display_admin_settings');
		if ($current_user->ID != 0){
			$userRole = ($current_user->data->wp_capabilities);
			if ($userRole != NULL){
				$currentRole = key($userRole);
				$getSettings = $wppb_showAdminBar[$currentRole];
				if ($getSettings == 'show')
					return true;
				elseif ($getSettings == 'hide')
					return false;
			}
		}
	}
	else 
		return true;
}



if ( is_admin() ){
  
  /* register the settings for the menu only display sidebar menu for a user with a certain capability, in this case only the "admin" */
  add_action('admin_init', 'wppb_register_settings');
  
    $wppb_premiumAdmin = wppb_plugin_dir . '/premium/functions/';	
    if (file_exists ( $wppb_premiumAdmin.'premium.functions.load.php' )){
      include_once($wppb_premiumAdmin.'premium.functions.load.php');    
	  
      /* check whether a delete attachment has been requested */
      add_action('admin_init', 'deleteAttachment');  
  
      /* check whether a delete avatar has been requested */
      add_action('admin_init', 'deleteAvatar');
  
  }
  

  /* display the same extra profile fields in the admin panel also */
  $wppb_premium = wppb_plugin_dir . '/premium/functions/';
  if (file_exists ( $wppb_premium.'extra.fields.php' )){
		include( $wppb_premium.'extra.fields.php' );
		add_action( 'show_user_profile', 'display_profile_extra_fields', 10 );
		add_action( 'edit_user_profile', 'display_profile_extra_fields', 10 );
		add_action( 'personal_options_update', 'save_extra_profile_fields', 10 );
		add_action( 'edit_user_profile_update', 'save_extra_profile_fields', 10 );
		
		/* check to see if the inserted serial number is valid or not; purely for visual needs */
		add_action('admin_init', 'wppb_check_serial_number');
  }

}
else if ( !is_admin() ){
  /* include the stylesheet */
  add_action('wp_print_styles', 'wppb_add_plugin_stylesheet');		
  
  $wppb_plugin = wppb_plugin_dir . '/';
  
  /* include the menu file for the profile informations */
  include_once($wppb_plugin.'front-end/wppb.edit.profile.php');        		 
  add_shortcode('wppb-edit-profile', 'wppb_front_end_profile_info');
  
  /*include the menu file for the login screen */
  include_once($wppb_plugin.'front-end/wppb.login.php');       
  add_shortcode('wppb-login', 'wppb_front_end_login');
  
  /* include the menu file for the register screen */
  include_once($wppb_plugin.'front-end/wppb.register.php');        		
  add_shortcode('wppb-register', 'wppb_front_end_register');
  
  /* set the front-end admin bar to show/hide */
  add_filter( 'show_admin_bar' , 'wppb_show_admin_bar');

  /* Shortcodes used for the widget area. Just uncomment whichever you need */
  add_filter('widget_text', 'do_shortcode', 11);
  
  /* check to see if the premium functions are present */
  $wppb_premiumAdmin = wppb_plugin_dir . '/premium/functions/';	
  if (file_exists ( $wppb_premiumAdmin.'premium.functions.load.php' )){
  
      include_once($wppb_premiumAdmin.'premium.functions.load.php');    
	  
	  /* filter to set current users custom avatar */
      add_filter('get_avatar', 'changeDefaultAvatar', 21, 5);
	  
	  /* check if there is a need to resize the current avatar image for all the users*/
	  add_action('init', 'wppb_resize_avatar');
  }
}