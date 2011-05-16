<?php
/*
Plugin Name: Profile Builder
Plugin URI: http://www.cozmoslabs.com/2011/04/12/wordpress-profile-builder-a-front-end-user-registration-login-and-edit-profile-plugin/
Description: Login, registration and edit profile shortcodes for the front-end. Also you can chose what fields should be displayed.
Version: 1.0.10
Author: Reflection Media
Author URI: http://reflectionmedia.ro
License: GPL2

== Copyright ==
Copyright 2011 Reflection Media (wwww.reflectionmedia.ro)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

register_activation_hook( __FILE__ , 'wppb_initialize_variables' );           //initialize some values upon plug-in activation

	function wppb_initialize_variables(){
	$wppb_default_settings = array(  'username' => 'show', 
							  'firstname'=> 'show',
							  'lastname' => 'show',
							  'nickname' => 'show',
							  'dispname' => 'show',
							  'email'	 => 'show',
							  'website'  => 'show',
							  'aim' 	 => 'show',
							  'yahoo'    => 'show',
							  'jabber'	 => 'show',
							  'bio'		 => 'show',
							  'password' => 'show' );
		add_option( 'wppb_default_settings', $wppb_default_settings );    //set all fields visible on first activation of the plugin
		add_option( 'wppb_default_style', 'yes');
		
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters('editable_roles', $all_roles);

		$admintSettingsPresent = get_option('wppb_display_admin_settings','not_found');

		if ($admintSettingsPresent == 'not_found'){                    			 // if the field doesn't exists, then create it
			$rolesArray = array();
			foreach ( $editable_roles as $key => $data )
				$rolesArray = array( $key => 'show' ) + $rolesArray;
			$rolesArray = array_reverse($rolesArray,true);
			add_option( 'wppb_display_admin_settings', $rolesArray);
		}
	}

	
function wppb_create_menu(){
	add_submenu_page('users.php', 'Profile Builder', 'Profile Builder', 'delete_users', 'ProfileBuilderSettings', 'wppb_display_menu');
}


function wppb_register_settings() { 									// whitelist options, you can add more register_settings changing the second parameter
	register_setting( 'wppb-option-group', 'wppb_default_settings' );
	register_setting( 'wppb_default_style', 'wppb_default_style' );
	register_setting( 'wppb_display_admin_settings', 'wppb_display_admin_settings' );
}


function wppb_add_plugin_stylesheet() {
		$wppb_showDefaultCss = get_option('wppb_default_style');
        $styleUrl = WP_PLUGIN_URL . '/profile-builder/css/style.css';
        $styleFile = WP_PLUGIN_DIR . '/profile-builder/css/style.css';
        if ( file_exists($styleFile) && $wppb_showDefaultCss == 'yes') {
            wp_register_style('wppb_stylesheet', $styleUrl);
            wp_enqueue_style( 'wppb_stylesheet');
        }
}


function wppb_show_admin_bar($content){
	global $current_user;
	$admintSettingsPresent = get_option('wppb_display_admin_settings','not_found');
	if ($admintSettingsPresent != 'not_found'){    
		$wppb_showAdminBar = get_option('wppb_display_admin_settings');
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
	else 
		return true;
}


if (is_admin() ){ 											            // if we are in the admin menu
	include_once('includes/wppb-menu-file.php');                		// include the menu file
	add_action('admin_init', 'wppb_register_settings');          		// register the settings for the menu only display sidebar menu for a user with a certain capability, in this case only the "admin"
	add_action('admin_menu','wppb_create_menu');						// call the wppb_create_menu function
}else{                                                         		    // if we aren't in the admin back-end menu, aka we are in the front-end view
    add_action('wp_print_styles', 'wppb_add_plugin_stylesheet');		// include the standard style-sheet or specify the path to a new one
	include_once('includes/wppb-front-end-profile.php');        		// include the menu file for the profile informations
	add_shortcode('wppb-edit-profile', 'wppb_front_end_profile_info');
	include_once('includes/wppb-front-end-login.php');        			// include the menu file for the login screen
	add_shortcode('wppb-login', 'wppb_front_end_login');
	include_once('includes/wppb-front-end-register.php');        		// include the menu file for the register screen
	add_shortcode('wppb-register', 'wppb_front_end_register');
	add_filter( 'show_admin_bar' , 'wppb_show_admin_bar');				// set the front-end admin bar to show/hide

	/* Allow shortcodes to be used in the text widgets */
	add_filter('widget_text', 'do_shortcode');
}
