<?php

if( !defined( 'WP_UNINSTALL_PLUGIN' ) )			
	exit ();									// If uninstall not called from WordPress exit

delete_option( 'wppb_default_settings' );	    // Delete default settings from options table
delete_option( 'wppb_default_style' );			// Delete "use default css or not" settings
delete_option( 'wppb_display_admin_settings' ); // Delete display admin bar option

?>