<?php
/*
Plugin Name: Profile Builder
Plugin URI: http://www.cozmoslabs.com/2011/04/12/wordpress-profile-builder-a-front-end-user-registration-login-and-edit-profile-plugin/
Description: Login, registration and edit profile shortcodes for the front-end. Also you can chose what fields should be displayed or add new (custom) ones both in the front-end and in the dashboard.
Version: 1.1.58
Author: Reflection Media, Barina Gabriel
Author URI: http://www.reflectionmedia.ro
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

 /*
Original Plugin Name: OptionTree
Original Plugin URI: http://wp.envato.com
Original Author: Derek Herman
Original Author URI: http://valendesigns.com
*/

/**
 * Definitions
 *
 *
 */

function wppb_return_bytes( $val ) {
    $val = trim( $val );
    $last = strtolower( $val[strlen( $val )-1] );
    switch( $last ) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}
 
define( 'PROFILE_BUILDER_VERSION', '1.1.58' );
define( 'WPPB_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'WPPB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE', apply_filters( 'wppb_server_max_upload_size_byte_constant', wppb_return_bytes( ini_get( 'upload_max_filesize') ) ) );
define( 'WPPB_SERVER_MAX_UPLOAD_SIZE_MEGA', apply_filters( 'wppb_server_max_upload_size_mega_constant', ini_get( 'upload_max_filesize') ) );
define( 'WPPB_SERVER_MAX_POST_SIZE_BYTE', apply_filters( 'wppb_server_max_post_size_byte_constant', wppb_return_bytes( ini_get( 'post_max_size') ) ) );
define( 'WPPB_SERVER_MAX_POST_SIZE_MEGA', apply_filters( 'wppb_server_max_post_size_mega_constant', ini_get( 'post_max_size') ) );
define( 'WPPB_TRANSLATE_DIR', WPPB_PLUGIN_DIR.'/translation' );
define( 'WPPB_TRANSLATE_DOMAIN', 'profilebuilder' );


//pre-require the functions file needed for the wppb_updated_patch function
require_once( 'functions/functions.load.php' );

if (file_exists ( WPPB_PLUGIN_DIR . '/premium/addons/addon.php' ) ){
	wppb_update_patch( 'pro' );
}elseif ( file_exists ( WPPB_PLUGIN_DIR . '/premium/functions/premium.functions.load.php' ) ){
	wppb_update_patch( 'hobbyist' );
}else{
	wppb_update_patch( 'free' );
}


/**
 * Required Files
 *
 *
 */
require_once( 'functions/functions.load.php' );

$wppb_premiumAdmin = WPPB_PLUGIN_DIR . '/premium/classes/';	
if (file_exists ( $wppb_premiumAdmin.'premium.class.admin.php' ) ){
	require_once( $wppb_premiumAdmin.'premium.class.admin.php' );
}else{
	require_once( 'classes/class.admin.php' );
}

/* check for updates */
$wppb_premiumUpdate = WPPB_PLUGIN_DIR.'/premium/update/';
if (file_exists ( $wppb_premiumUpdate.'update-checker.php' ) ){
	require ($wppb_premiumUpdate.'update-checker.php' );
	
	if (file_exists ( WPPB_PLUGIN_DIR . '/premium/addons/addon.php' ) ){
		$localSerial = get_option( 'wppb_profile_builder_pro_serial' );
		$wppb_update = new wppb_PluginUpdateChecker( 'http://updatemetadata.cozmoslabs.com/?localSerialNumber='.$localSerial.'&uniqueproduct=RMPB', __FILE__, 'profile-builder-pro-update' );
	
	}else{
		$localSerial = get_option( 'wppb_profile_builder_hobbyist_serial' );
		$wppb_update = new wppb_PluginUpdateChecker( 'http://updatemetadata.cozmoslabs.com/?localSerialNumber='.$localSerial.'&uniqueproduct=RMPBH', __FILE__, 'profile-builder-hobbyist-update' );
	}
}


/**
 * Initialize the translation for the Plugin.
 *
 */
function wppb_init_translation(){
	load_plugin_textdomain( 'profilebuilder', false, basename( dirname( __FILE__ ) ) . '/translation/' ); 
}
add_action( 'init', 'wppb_init_translation' );

/**
 * Instantiate Class
 *
 *
 */
$PB_Admin = new PB_Admin();

/**
 * Wordpress Activate/Deactivate
 *
 * @uses register_activation_hook()
 * @uses register_deactivation_hook()
 *
 *
 */
register_activation_hook( __FILE__, array( $PB_Admin, 'profile_builder_activate' ) );
register_deactivation_hook( __FILE__, array( $PB_Admin, 'profile_builder_deactivate' ) );

/**
 * Required action filters
 *
 * @uses add_action()
 *
 *
 */
add_action( 'admin_init', array( $PB_Admin, 'profile_builder_initialize' ) );
add_action( 'admin_menu', array( $PB_Admin, 'profile_builder_admin' ) );
add_action( 'wp_ajax_profile_builder_add', array( $PB_Admin, 'profile_builder_add' ) );
add_action( 'wp_ajax_profile_builder_edit', array( $PB_Admin, 'profile_builder_edit' ) );
add_action( 'wp_ajax_profile_builder_delete', array( $PB_Admin, 'profile_builder_delete' ) );
add_action( 'wp_ajax_profile_builder_next_id', array( $PB_Admin, 'profile_builder_next_id' ) );
add_action( 'wp_ajax_profile_builder_sort', array( $PB_Admin, 'profile_builder_sort' ) );