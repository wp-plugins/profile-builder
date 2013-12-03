function wppb_display_page_select( value ) {
	if ( value == 'yes' )
		jQuery ( '#framework_wrap #content #general_settings_registration_page_div' ).show();
	else
		jQuery ( '#framework_wrap #content #general_settings_registration_page_div' ).hide();
}



jQuery(function() {
	if ( ( jQuery( '#framework_wrap #content .wppb_general_settings2' ).val() == 'yes' ) || ( jQuery( '#framework_wrap #content #wppb_general_settings_hidden' ).val() == 'multisite' ) )
		jQuery ( '#framework_wrap #content #general_settings_registration_page_div' ).show();
	else
		jQuery ( '#framework_wrap #content #general_settings_registration_page_div' ).hide();
});