<?php
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )			
	exit ();												// If uninstall not called from WordPress exit

delete_option( 'wppb_default_settings' );	   	 			// Delete default settings from options table
delete_option( 'wppb_default_style' );						// Delete "use default css or not" settings
delete_option( 'wppb_display_admin_settings' ); 			// Delete display admin bar option
delete_option( 'wppb_custom_fields' ); 						// Delete the default fields
delete_option( 'wppb_profile_builder_pro_serial' ); 		// Delete the serial number associated with this instalation
delete_option( 'serial_number_availability' ); 				// Delete the serial number status
delete_option( 'wppb_premium_addon_settings' ); 			// Delete addon settings
delete_option( 'wppb_premium_addon_settings_description' ); // Delete addon settings description
delete_option( 'customRedirectSettings' ); 					// Delete the custom redirect settings
delete_option( 'userListingSettings' ); 					// Delete the user-listing settings


/* delete all the custom fields */
global $wpdb;
$allUserMeta = $wpdb->get_results("SELECT * FROM $wpdb->usermeta");

foreach ($allUserMeta as $userMeta) {
	$found = strpos ( $userMeta->meta_key , 'custom_field_' );
	if ( $found !== FALSE ){
		$metaFieldName = $userMeta->meta_key;
		$metaFieldValue = $userMeta->meta_value;
		$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key = '".$metaFieldName."'	AND meta_value = '".$metaFieldValue."'");
	}

	$foundAvatar = strpos ( $userMeta->meta_key , 'custom_field_resized' );
	if ( $foundAvatar !== FALSE ){
		$metaFieldName = $userMeta->meta_key;
		$metaFieldValue = $userMeta->meta_value;
		$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key = '".$metaFieldName."'	AND meta_value = '".$metaFieldValue."'");
	}
}