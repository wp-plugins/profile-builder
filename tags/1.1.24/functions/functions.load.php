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
	$premiumPresent = wppb_plugin_dir . '/premium/premium.php';
	$addonPresent = wppb_plugin_dir . '/premium/addon/addon.php';
	
	register_setting( 'wppb_option_group', 'wppb_default_settings' );
	register_setting( 'wppb_default_style', 'wppb_default_style' );
	register_setting( 'wppb_display_admin_settings', 'wppb_display_admin_settings' );
	if (file_exists($premiumPresent)){
		register_setting( 'wppb_profile_builder_pro_serial', 'wppb_profile_builder_pro_serial' );
	}
	if (file_exists($addonPresent)){
		register_setting( 'wppb_premium_addon_settings', 'wppb_premium_addon_settings' );
		register_setting( 'customRedirectSettings', 'customRedirectSettings' );
		register_setting( 'userListingSettings', 'userListingSettings' );
	}
	
	
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
	global $wpdb;
	$admintSettingsPresent = get_option('wppb_display_admin_settings','not_found');

	if ($admintSettingsPresent != 'not_found'){
		if ($current_user->ID != 0){
			$capabilityName = $wpdb->prefix.'capabilities';
			$userRole = ($current_user->data->$capabilityName);
			if ($userRole != NULL){
				$currentRole = key($userRole);
				$getSettings = $admintSettingsPresent[$currentRole];
				if ($getSettings == 'show')
					return true;
				elseif ($getSettings == 'hide')
					return false;
			}elseif ($userRole == NULL){ // this is for the WP v.3.3
				$userRole = ($current_user->roles[0]);
				if ($userRole != NULL){
					$getSettings = $admintSettingsPresent[$userRole];
					if ($getSettings == 'show')
						return true;
					elseif ($getSettings == 'hide')
						return false;
				}
			}else
				return true;
		}
	}
	else
		return true;
}

if(!function_exists('wppb_curpageurl')){
    function wppb_curpageurl() {
     $pageURL = 'http';
     if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")) {
		$pageURL .= "s";
	 }
     $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }
     return $pageURL;
    }
}



if ( is_admin() ){
   /* include the css for the datepicker */
   $wppb_premiumDatepicker = wppb_plugin_dir . '/premium/assets/css/';
   if (file_exists ( $wppb_premiumDatepicker.'datepicker.style.css' ))
		wp_enqueue_style( 'profile-builder-admin-datepicker-style', wppb_plugin_url.'/premium/assets/css/datepicker.style.css', false, ProfileBuilderVersion);

 
  
  /* register the settings for the menu only display sidebar menu for a user with a certain capability, in this case only the "admin" */
  add_action('admin_init', 'wppb_register_settings');
  
    $wppb_premiumAdmin = wppb_plugin_dir . '/premium/functions/';	
    if (file_exists ( $wppb_premiumAdmin.'premium.functions.load.php' )){
      include_once($wppb_premiumAdmin.'premium.functions.load.php');    
	  
      /* check whether a delete attachment has been requested */
      add_action('admin_init', 'wppb_deleteAttachment');  
  
      /* check whether a delete avatar has been requested */
      add_action('admin_init', 'wppb_deleteAvatar');
  
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
	
	/* include the menu file for the recover password screen */
	include_once($wppb_plugin.'front-end/wppb.recover.password.php');        		
	add_shortcode('wppb-recover-password', 'wppb_front_end_password_recovery');

	/* set the front-end admin bar to show/hide */
	add_filter( 'show_admin_bar' , 'wppb_show_admin_bar');

	/* Shortcodes used for the widget area. Just uncomment whichever you need */
	add_filter('widget_text', 'do_shortcode', 11);

	/* check to see if the premium functions are present */
	$wppb_premiumAdmin = wppb_plugin_dir . '/premium/functions/';	
	if (file_exists ( $wppb_premiumAdmin.'premium.functions.load.php' )){

		include_once($wppb_premiumAdmin.'premium.functions.load.php');    

		/* filter to set current users custom avatar */
		add_filter('get_avatar', 'wppb_changeDefaultAvatar', 21, 5);

		/* check if there is a need to resize the current avatar image for all the users*/
		add_action('init', 'wppb_resize_avatar');
	}

	$wppb_premiumAddon = wppb_plugin_dir . '/premium/addon/';	
	if (file_exists ( $wppb_premiumAddon.'addon.functions.php' )){
		//include the file containing the addon functions
		include_once($wppb_premiumAddon.'addon.functions.php');    

		$wppb_addonOptions = get_option('wppb_premium_addon_settings');
		if ($wppb_addonOptions['userListing'] == 'show'){
		  //add shortcode for the user-listing functionality
		  add_shortcode('wppb-list-users', 'wppb_list_all_users');
		}
	}
}