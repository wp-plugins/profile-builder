<?php if (!defined('PROFILE_BUILDER_VERSION')) exit('No direct script access allowed');
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
 
// whitelist options, you can add more register_settings changing the second parameter
function wppb_register_settings() {
	register_setting( 'wppb_option_group', 'wppb_default_settings' );
	register_setting( 'wppb_general_settings', 'wppb_general_settings' );
	register_setting( 'wppb_display_admin_settings', 'wppb_display_admin_settings' );
	register_setting( 'wppb_profile_builder_pro_serial', 'wppb_profile_builder_pro_serial' );
	register_setting( 'wppb_profile_builder_hobbyist_serial', 'wppb_profile_builder_hobbyist_serial' );
	register_setting( 'wppb_addon_settings', 'wppb_addon_settings' );
	register_setting( 'customRedirectSettings', 'customRedirectSettings' );
	register_setting( 'customUserListingSettings', 'customUserListingSettings' );
	register_setting( 'reCaptchaSettings', 'reCaptchaSettings' );
	register_setting( 'emailCustomizer', 'emailCustomizer' );
}


// include files
$wppb_premiumAddon = WPPB_PLUGIN_DIR . '/premium/addons/';
$wppb_premiumAdmin = WPPB_PLUGIN_DIR . '/premium/functions/';	
	
if (file_exists ( $wppb_premiumAddon.'recaptcha.php' ))
	include_once($wppb_premiumAddon.'recaptcha.php');
if (file_exists ( $wppb_premiumAddon.'custom.redirects.php' ))
	include_once($wppb_premiumAddon.'custom.redirects.php');
if (file_exists ( $wppb_premiumAddon.'email.customizer.php' ))
	include_once($wppb_premiumAddon.'email.customizer.php');
if (file_exists ( $wppb_premiumAddon.'userlisting.php' )){
	include_once($wppb_premiumAddon.'userlisting.php');  

	$wppb_addonOptions = get_option('wppb_addon_settings');
	if ($wppb_addonOptions['wppb_userListing'] == 'show'){
	  add_shortcode('wppb-list-users', 'wppb_list_all_users');
	}else
		add_shortcode('wppb-list-users', 'wppb_list_all_users_display_error');
}
if (file_exists ( $wppb_premiumAdmin.'premium.functions.load.php' )){
	include_once($wppb_premiumAdmin.'premium.functions.load.php');    
	add_filter('get_avatar', 'wppb_changeDefaultAvatar', 21, 5);
}   
if (file_exists ( $wppb_premiumAdmin.'admin.approval.php' )){
	include_once($wppb_premiumAdmin.'admin.approval.php');    
}
if (file_exists ( $wppb_premiumAdmin.'login.widget.php' )){
	include_once($wppb_premiumAdmin.'login.widget.php');    
}
if (file_exists ( $wppb_premiumAdmin.'register.version.php' ))
	include_once($wppb_premiumAdmin.'register.version.php');	

require_once(WPPB_PLUGIN_DIR.'/functions/basic.info.php');
require_once(WPPB_PLUGIN_DIR.'/functions/general.settings.php');
require_once(WPPB_PLUGIN_DIR.'/functions/admin.bar.php');
require_once(WPPB_PLUGIN_DIR.'/classes/class.email.confirmation.php');
require_once(WPPB_PLUGIN_DIR.'/classes/class.bulk.approve.unapprove.php');
require_once(WPPB_PLUGIN_DIR.'/functions/email.confirmation.php');
require_once(WPPB_PLUGIN_DIR.'/functions/default.settings.php');


// WPML support
function wppb_icl_t($context, $name, $value){  
if(function_exists('icl_t'))
	return icl_t($context, $name, $value);
else
	return $value;
}


function wppb_add_plugin_stylesheet() {
		$wppb_generalSettings = get_option('wppb_general_settings');
		
        $styleUrl_default = WPPB_PLUGIN_URL . 'assets/css/front.end.css';
        $styleUrl_white = WPPB_PLUGIN_URL . 'premium/assets/css/front.end.white.css';
        $styleUrl_black = WPPB_PLUGIN_URL . 'premium/assets/css/front.end.black.css';
        $styleFile_default = WPPB_PLUGIN_DIR . '/assets/css/front.end.css';
        $styleFile_white = WPPB_PLUGIN_DIR . '/premium/assets/css/front.end.white.css';
        $styleFile_black = WPPB_PLUGIN_DIR . '/premium/assets/css/front.end.black.css';
        if ( (file_exists($styleFile_default)) && ($wppb_generalSettings['extraFieldsLayout'] == 'yes') ) {
            wp_register_style('wppb_stylesheet', $styleUrl_default);
            wp_enqueue_style( 'wppb_stylesheet');
        }elseif ( (file_exists($styleFile_white)) && ($wppb_generalSettings['extraFieldsLayout'] == 'white') ) {
            wp_register_style('wppb_stylesheet', $styleUrl_white);
            wp_enqueue_style( 'wppb_stylesheet');
        }elseif ( (file_exists($styleFile_black)) && ($wppb_generalSettings['extraFieldsLayout'] == 'black') ) {
            wp_register_style('wppb_stylesheet', $styleUrl_black);
            wp_enqueue_style( 'wppb_stylesheet');
        }
}


function wppb_show_admin_bar($content){
	global $current_user;

	$adminSettingsPresent = get_option('wppb_display_admin_settings','not_found');
	$show = null;

	if ($adminSettingsPresent != 'not_found' && $current_user->ID)
		foreach ($current_user->roles as $role_key) {
			if (empty($GLOBALS['wp_roles']->roles[$role_key]))
				continue;
			$role = $GLOBALS['wp_roles']->roles[$role_key];
			if (isset($adminSettingsPresent[$role['name']])) {
				if ($adminSettingsPresent[$role['name']] == 'show')
					$show = true;
				if ($adminSettingsPresent[$role['name']] == 'hide' && $show === null)
					$show = false;
			}
		}
	return $show === null ? $content : $show;
}


if(!function_exists('wppb_curpageurl')){
	function wppb_curpageurl() {
		$pageURL = 'http';
		
		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on"))
			$pageURL .= "s";
			
		$pageURL .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			
		else
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		
		return $pageURL;
	}
}


if ( is_admin() ){
	add_action('admin_enqueue_scripts', 'wppb_add_backend_style');
	function wppb_add_backend_style(){
		wp_enqueue_style( 'profile-builder-back-end-style', WPPB_PLUGIN_URL.'assets/css/back.end.css', false, PROFILE_BUILDER_VERSION);
		wp_enqueue_script( 'profile-builder-back-end-js', WPPB_PLUGIN_URL.'assets/js/back.end.js', false, PROFILE_BUILDER_VERSION );
	}

	// include the css for the datepicker
	$wppb_premiumDatepicker = WPPB_PLUGIN_DIR . '/premium/assets/css/';
	if (file_exists ( $wppb_premiumDatepicker.'datepicker.style.css' )){
		add_action('admin_enqueue_scripts', 'wppb_add_datepicker_style');
		function wppb_add_datepicker_style(){
			wp_enqueue_style( 'profile-builder-admin-datepicker-style', WPPB_PLUGIN_URL.'premium/assets/css/datepicker.style.css', false, PROFILE_BUILDER_VERSION);
		}
	}



	// register the settings for the menu only display sidebar menu for a user with a certain capability, in this case only the "admin"
	add_action('admin_init', 'wppb_register_settings');
  

	// display the same extra profile fields in the admin panel also
	$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
	if (file_exists ( $wppb_premium.'extra.fields.php' )){
		include_once( $wppb_premium.'extra.fields.php' );
		add_action( 'show_user_profile', 'display_profile_extra_fields_in_admin', 10 );
		add_action( 'edit_user_profile', 'display_profile_extra_fields_in_admin', 10 );
		add_action( 'personal_options_update', 'save_profile_extra_fields_in_admin', 10 );
		add_action( 'edit_user_profile_update', 'save_profile_extra_fields_in_admin', 10 );
	}

}else if ( !is_admin() ){
	// include the stylesheet
	add_action('wp_print_styles', 'wppb_add_plugin_stylesheet');		

	// include the menu file for the profile informations
	include_once(WPPB_PLUGIN_DIR.'/front-end/wppb.edit.profile.php');        		 
	add_shortcode('wppb-edit-profile', 'wppb_front_end_profile_info');

	// include the menu file for the login screen
	include_once(WPPB_PLUGIN_DIR.'/front-end/wppb.login.php');       
	add_shortcode('wppb-login', 'wppb_front_end_login');

	// include the menu file for the register screen
	include_once(WPPB_PLUGIN_DIR.'/front-end/wppb.register.php');        		
	add_shortcode('wppb-register', 'wppb_front_end_register_handler');	
	
	// include the menu file for the recover password screen
	include_once(WPPB_PLUGIN_DIR.'/front-end/wppb.recover.password.php');        		
	add_shortcode('wppb-recover-password', 'wppb_front_end_password_recovery');

	// set the front-end admin bar to show/hide
	add_filter( 'show_admin_bar' , 'wppb_show_admin_bar');

	// Shortcodes used for the widget area
	add_filter('widget_text', 'do_shortcode', 11);
}

// this is to assure backwards compatibility for future reference for all versions, starting from version 1.3.13
function wppb_update_patch( $plugin ){
	if( !get_option( 'wppb_version' ) ) {
		add_option( 'wppb_version', '1.3.13' );
		
		do_action( 'wppb_set_initial_version_number', PROFILE_BUILDER_VERSION );
	}
	
	$wppb_version = get_option( 'wppb_version' );
	
	do_action( 'wppb_before_default_changes', PROFILE_BUILDER_VERSION, $wppb_version );
	
	if( version_compare( PROFILE_BUILDER_VERSION, $wppb_version, '>' ) ) {
		if ( ( $plugin == 'pro' ) || ( $plugin == 'hobbyist' ) ){
			// this is to assure backwards compatibility from version 1.3.13 to version 1.3.14, we need to copy all data from item_options_values, and create the item_option_labels index for the checkbox, radio and select extra-fields, to reflect the front-end changes
			$custom_fields = get_option( 'wppb_custom_fields','not_found' );

			if ( $custom_fields != 'not_found' ){
				foreach ( $custom_fields as $key => $value ){
					if ( ( $value['item_type'] == 'checkbox' ) || ( $value['item_type'] == 'radio' ) || ( $value['item_type'] == 'select' ) ){
						if ( isset( $custom_fields[$key]['item_option_values'] ) ){
							$custom_fields[$key]['item_option_labels'] = $custom_fields[$key]['item_option_values'];
							unset( $custom_fields[$key]['item_option_values'] );
						}
						
					}else
						unset( $custom_fields[$key]['item_option_values'] );
				}
				
				update_option( 'wppb_custom_fields', $custom_fields );
			}
			// END this is to assure backwards compatibility from version 1.3.13 to version 1.3.14, we need to copy all data from item_options_values, and create the item_option_labels index for the checkbox, radio and select extra-fields, to reflect the front-end changes
		}
		
		if ( $plugin == 'pro' ){
			// this is to assure backwards compatibility from version 1.3.14 to version 1.3.15, where the email customizer array-indexes have been renamed
			$email_customizer_array = get_option( 'emailCustomizer', 'not_found' );

			if ( $email_customizer_array != 'not_found' ){
				$new_email_customizer_array = array();
				
				if ( isset( $email_customizer_array['from'] ) ){
					$new_email_customizer_array['reply_to'] = $email_customizer_array['from'];
					unset( $email_customizer_array['from'] );
				}
					
				if ( isset( $email_customizer_array['settingsGroup1Option2'] ) ){
					$new_email_customizer_array['default_registration_email_subject'] = $email_customizer_array['settingsGroup1Option2'];
					unset( $email_customizer_array['settingsGroup1Option2'] );
				}
					
				if ( isset( $email_customizer_array['settingsGroup1Option3'] ) ){
					$new_email_customizer_array['default_registration_email_content'] = $email_customizer_array['settingsGroup1Option3'];
					unset( $email_customizer_array['settingsGroup1Option3'] );
				}
					
				if ( isset( $email_customizer_array['settingsGroup3Option2'] ) ){
					$new_email_customizer_array['registration_w_admin_approval_email_subject'] = $email_customizer_array['settingsGroup3Option2'];
					unset( $email_customizer_array['settingsGroup3Option2'] );
				}
					
				if ( isset( $email_customizer_array['settingsGroup3Option3'] ) ){
					$new_email_customizer_array['registration_w_admin_approval_email_content'] = $email_customizer_array['settingsGroup3Option3'];
					unset( $email_customizer_array['settingsGroup3Option3'] );
				}
					
				if ( isset( $email_customizer_array['settingsGroup4Option2'] ) ){
					$new_email_customizer_array['admin_approval_aproved_status_email_subject'] = $email_customizer_array['settingsGroup4Option2'];
					unset( $email_customizer_array['settingsGroup4Option2'] );
				}
					
				if ( isset( $email_customizer_array['settingsGroup4Option3'] ) ){
					$new_email_customizer_array['admin_approval_aproved_status_email_content'] = $email_customizer_array['settingsGroup4Option3'];
					unset( $email_customizer_array['settingsGroup4Option3'] );
				}
			
				if ( isset( $email_customizer_array['settingsGroup2Option2'] ) ){
					$new_email_customizer_array['registration_w_email_confirmation_email_subject'] = $email_customizer_array['settingsGroup2Option2'];
					unset( $email_customizer_array['settingsGroup2Option2'] );
				}
			
				if ( isset( $email_customizer_array['settingsGroup2Option3'] ) ){
					$new_email_customizer_array['registration_w_email_confirmation_email_content'] = $email_customizer_array['settingsGroup2Option3'];
					unset( $email_customizer_array['settingsGroup2Option3'] );
				}
					
				if ( isset( $email_customizer_array['settingsGroup4Option6'] ) ){
					$new_email_customizer_array['admin_approval_unaproved_status_email_subject'] = $email_customizer_array['settingsGroup4Option6'];
					unset( $email_customizer_array['settingsGroup4Option6'] );
				}
					
				if ( isset( $email_customizer_array['settingsGroup4Option7'] ) ){
					$new_email_customizer_array['admin_approval_unaproved_status_email_content'] = $email_customizer_array['settingsGroup4Option7'];
					unset( $email_customizer_array['settingsGroup4Option7'] );
				}
					
				if ( isset( $email_customizer_array['admin_settingsGroup1Option2'] ) ){
					$new_email_customizer_array['admin_default_registration_email_subject'] = $email_customizer_array['admin_settingsGroup1Option2'];
					unset( $email_customizer_array['admin_settingsGroup1Option2'] );
				}
					
				if ( isset( $email_customizer_array['admin_settingsGroup1Option3'] ) ){
					$new_email_customizer_array['admin_default_registration_email_content'] = $email_customizer_array['admin_settingsGroup1Option3'];
					unset( $email_customizer_array['admin_settingsGroup1Option3'] );
				}
					
				if ( isset( $email_customizer_array['admin_settingsGroup3Option2'] ) ){
					$new_email_customizer_array['admin_registration_w_admin_approval_email_subject'] = $email_customizer_array['admin_settingsGroup3Option2'];
					unset( $email_customizer_array['admin_settingsGroup3Option2'] );
				}
					
				if ( isset( $email_customizer_array['admin_settingsGroup3Option3'] ) ){
					$new_email_customizer_array['admin_registration_w_admin_approval_email_content'] = $email_customizer_array['admin_settingsGroup3Option3'];
					unset( $email_customizer_array['admin_settingsGroup3Option3'] );
				}

				update_option( 'emailCustomizer', $new_email_customizer_array + $email_customizer_array );
			}
			// END this is to assure backwards compatibility from version 1.3.14 to version 1.3.15, where the email customizer array-indexes have been renamed
		}
		
		update_option( 'wppb_version', PROFILE_BUILDER_VERSION );
	}
	
	do_action( 'wppb_after_default_changes', PROFILE_BUILDER_VERSION, $wppb_version );	
}

// functions to set email from and reply-to
function wppb_website_email($sender_email){
	$wppb_addonOptions = get_option( 'wppb_addon_settings' );
	
	if ( ( $wppb_addonOptions['wppb_emailCustomizer'] == 'show' ) || ( $wppb_addonOptions['wppb_emailCustomizerAdmin'] == 'show' ) ){
		$email_customizer_array = get_option( 'emailCustomizer', 'not_found' );
		
		if ( $email_customizer_array != 'not_found' )
			$sender_email = str_replace( '%%reply_to%%', $sender_email, $email_customizer_array['reply_to'] );
		
	}

    return $sender_email = apply_filters('website_email_filter', $sender_email);
}
add_filter('wp_mail_from','wppb_website_email');
 
function wppb_website_name($site_name){
	$wppb_addonOptions = get_option( 'wppb_addon_settings' );
	
	if ( ( $wppb_addonOptions['wppb_emailCustomizer'] == 'show' ) || ( $wppb_addonOptions['wppb_emailCustomizerAdmin'] == 'show' ) ){
		$email_customizer_array = get_option( 'emailCustomizer', 'not_found' );
		
		if ( $email_customizer_array != 'not_found' )
			$site_name = str_replace( '%%site_name%%', $site_name, $email_customizer_array['from_name'] );
		
	}

    return $site_name = apply_filters( 'website_email_filter', $site_name );
}
add_filter('wp_mail_from_name','wppb_website_name');

// function to send out emails (depending on the case, set by $function), and if needed overwrite it with the data storder in via email customizer
function wppb_mail($to, $subject, $message, $message_from){
	$to = apply_filters ( 'wppb_send_email_to', $to );
	$send_email = apply_filters ( 'wppb_send_email', true, $to, $subject, $message );
	
	do_action( 'wppb_before_sending_email', $to, $subject, $message, $send_email );
	
	if ( $send_email ){
		//we add this filter to enable html encoding
		add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );	
		
		$sent = wp_mail( $to , $subject, wpautop($message, true) );
	}
	
	do_action( 'wppb_after_sending_email', $sent, $to, $subject, $message, $send_email );
	
	return $sent;
}

function wppb_activate_account_check(){
	if ( ( isset( $_GET['activation_key'] ) ) && ( trim( $_GET['activation_key'] ) != '' ) ){
		global $post;

		$wppb_generalSettings = get_option( 'wppb_general_settings' );
		$activation_landing_page_id = ( ( isset( $wppb_generalSettings['activationLandingPage'] ) && ( trim( $wppb_generalSettings['activationLandingPage'] ) != '' ) ) ? $wppb_generalSettings['activationLandingPage'] : 'not_set' );
		
		if ( $activation_landing_page_id != 'not_set' ){
			//an activation page was selected, but we still need to check if the current page doesn't already have the registration shortcode
			if ( strpos( $post->post_content, '[wppb-register' ) === false )
				add_filter( 'the_content', 'wppb_add_activation_message' );

		}elseif ( strpos( $post->post_content, '[wppb-register' ) === false ){
			//no activation page was selected, and the sent link pointed to the home url
			wp_redirect( apply_filters( 'wppb_activatate_account_redirect_url', WPPB_PLUGIN_URL.'assets/misc/wppb.fallback.page.php?activation_key='.urlencode( $_GET['activation_key'] ).'&site_name='.urlencode( get_bloginfo( 'name' ) ).'&site_url='.urlencode( get_bloginfo( 'url' ) ).'&message='.urlencode( $activation_message = wppb_activate_signup( $_GET['activation_key'] ) ), $_GET['activation_key'], $activation_message ) ); 
			exit;
		}
	}
}
add_action( 'template_redirect', 'wppb_activate_account_check' );


function wppb_add_activation_message( $content ){

	return wppb_activate_signup( $_GET['activation_key'] ) . $content;
}