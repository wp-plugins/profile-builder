<?php
/* handle field output */
function wppb_display_name_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){	
	$item_title = apply_filters( 'wppb_'.$form_location.'_display-name_item_title', wppb_icl_t( 'plugin profile-builder-pro', 'default_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
	$item_description = wppb_icl_t( 'plugin profile-builder-pro', 'default_field_'.$field['id'].'_description_translation', $field['description'] );
	$input_value = '';
	if( $form_location == 'edit_profile' )
		$input_value = get_the_author_meta( 'display_name', $user_id );
	
	if ( trim( $input_value ) == '' )
		$input_value = $field['default-value'];
		
	$input_value = ( isset( $request_data['display-name'] ) ? trim( $request_data['display-name'] ) : $input_value );
	
	if ( $form_location != 'back_end' ){
		$error_mark = ( ( $field['required'] == 'Yes' ) ? '<span class="wppb-required" title="'.wppb_required_field_error($field["field-title"]).'">*</span>' : '' );
					
		if ( array_key_exists( $field['id'], $field_check_errors ) )
			$error_mark = '<img src="'.WPPB_PLUGIN_URL.'assets/images/pencil_delete.png" title="'.wppb_required_field_error($field["field-title"]).'"/>';

        $output = '
			<label for="display-name">'.$item_title.$error_mark.'</label>
			<input class="text-input default_field_display-name" name="display-name" maxlength="'. apply_filters( 'wppb_maximum_character_length', 70 ) .'" type="text" id="display-name" value="'. esc_attr( wp_unslash( $input_value ) ) .'" />';
        if( !empty( $item_description ) )
            $output .= '<span class="wppb-description-delimiter">'. $item_description .'</span>';

	}
		
	return apply_filters( 'wppb_'.$form_location.'_display-name', $output, $form_location, $field, $user_id, $field_check_errors, $request_data );
}
add_filter( 'wppb_output_form_field_default-display-name-publicly-as', 'wppb_display_name_handler', 10, 6 );


/* handle field validation */
function wppb_check_display_name_value( $message, $field, $request_data, $form_location ){	
	if ( ( isset( $request_data['display-name'] ) && ( trim( $request_data['display-name'] ) == '' ) ) && ( $field['required'] == 'Yes' ) )
		return wppb_required_field_error($field["field-title"]);

    return $message;
}
add_filter( 'wppb_check_form_field_default-display-name-publicly-as', 'wppb_check_display_name_value', 10, 4 );


/* handle field save */
function wppb_userdata_add_display_name( $userdata, $global_request ){
	if ( isset( $global_request['display-name'] ) )
		$userdata['display-name'] = trim( sanitize_text_field( $global_request['display-name'] ) );
		
	return $userdata;
}
add_filter( 'wppb_build_userdata', 'wppb_userdata_add_display_name', 10, 2 );