<?php
/**
 * Function that creates the Manage Fields submenu and populates it with a repeater field form
 *
 * @since v.2.0
 *
 * @return void
 */
function wppb_manage_fields_submenu(){
	// create a new sub_menu page which holds the data for the default + extra fields
	$args = array(
				'menu_title' 	=> __( 'Manage Fields', 'profilebuilder' ),						
				'page_title' 	=> __( 'Manage Default and Extra Fields', 'profilebuilder' ),						
				'menu_slug'		=> 'manage-fields',
				'page_type'		=> 'submenu_page',
				'capability'	=> 'manage_options',
				'priority'		=> 5,
				'parent_slug'	=> 'profile-builder'
			);
	$all_fields = new WCK_Page_Creator_PB( $args );

	
	// populate this page
	$manage_field_types[] = 'Default - Name (Heading)';
	$manage_field_types[] = 'Default - Contact Info (Heading)';
	$manage_field_types[] = 'Default - About Yourself (Heading)';
	$manage_field_types[] = 'Default - Username';
	$manage_field_types[] = 'Default - First Name';
	$manage_field_types[] = 'Default - Last Name';
	$manage_field_types[] = 'Default - Nickname';
	$manage_field_types[] = 'Default - E-mail';
	$manage_field_types[] = 'Default - Website';

	// Default contact methods were removed in WP 3.6. A filter dictates contact methods.
	if ( apply_filters( 'wppb_remove_default_contact_methods', get_site_option( 'initial_db_version' ) < 23588 ) ){
		$manage_field_types[] = 'Default - AIM';
		$manage_field_types[] = 'Default - Yahoo IM';
		$manage_field_types[] = 'Default - Jabber / Google Talk';
	}

    $manage_field_types[] = 'Default - Password';
    $manage_field_types[] = 'Default - Repeat Password';
    $manage_field_types[] = 'Default - Biographical Info';
    $manage_field_types[] = 'Default - Display name publicly as';

    if( PROFILE_BUILDER != 'Profile Builder Free' ) {
        $manage_field_types[] = 'Heading';
        $manage_field_types[] = 'Input';
        $manage_field_types[] = 'Input (Hidden)';
        $manage_field_types[] = 'Textarea';
        $manage_field_types[] = 'WYSIWYG';
        $manage_field_types[] = 'Select';
        $manage_field_types[] = 'Select (Multiple)';
        $manage_field_types[] = 'Select (Country)';
        $manage_field_types[] = 'Select (Timezone)';
        $manage_field_types[] = 'Select (User Role)';
        $manage_field_types[] = 'Checkbox';
        $manage_field_types[] = 'Checkbox (Terms and Conditions)';
        $manage_field_types[] = 'Radio';
        $manage_field_types[] = 'Upload';
        $manage_field_types[] = 'Avatar';
        $manage_field_types[] = 'Datepicker';
        $manage_field_types[] = 'reCAPTCHA';
    }
	
				
	//Free to Pro call to action on Manage Fields page
	$field_description = 'Choose one of the supported field types';
	if( PROFILE_BUILDER == 'Profile Builder Free' ) {
		$field_description .='. Extra Field Types are available in %1$sHobbyist or PRO versions%2$s.';
	}

    //user roles
    global $wp_roles;

    $user_roles = array();
    foreach( $wp_roles->roles as $user_role_slug => $user_role )
        if( $user_role_slug !== 'administrator' )
            array_push( $user_roles, '%' . $user_role['name'] . '%' . $user_role_slug );

	// set up the fields array
	$fields = apply_filters( 'wppb_manage_fields', array(

        array( 'type' => 'text', 'slug' => 'field-title', 'title' => __( 'Field Title', 'profilebuilder' ), 'description' => __( 'Title of the field', 'profilebuilder' ) ),
        array( 'type' => 'select', 'slug' => 'field', 'title' => __( 'Field', 'profilebuilder' ), 'options' => apply_filters( 'wppb_manage_fields_types', $manage_field_types ), 'default-option' => true, 'description' => sprintf(__( $field_description, 'profilebuilder' ), '<a href="http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wpbackend&utm_medium=clientsite&utm_content=manage-fields-link&utm_campaign=PBFree">', '</a>') ),
        array( 'type' => 'text', 'slug' => 'meta-name', 'title' => __( 'Meta-name', 'profilebuilder' ), 'default' => wppb_get_meta_name(), 'description' => __( 'Use this in conjuction with WordPress functions to display the value in the page of your choosing<br/>Auto-completed but in some cases editable (in which case it must be uniqe)<br/>Changing this might take long in case of a very big user-count', 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'id', 'title' => __( 'ID', 'profilebuilder' ), 'default' => wppb_get_unique_id(), 'description' => __( "A unique, auto-generated ID for this particular field<br/>You can use this in conjuction with filters to target this element if needed<br/>Can't be edited", 'profilebuilder' ), 'readonly' => true ),
        array( 'type' => 'textarea', 'slug' => 'description', 'title' => __( 'Description', 'profilebuilder' ), 'description' => __( 'Enter a (detailed) description of the option for end users to read<br/>Optional', 'profilebuilder') ),
        array( 'type' => 'text', 'slug' => 'row-count', 'title' => __( 'Row Count', 'profilebuilder' ), 'default' => 5, 'description' => __( "Specify the number of rows for a 'Textarea' field<br/>If not specified, defaults to 5", 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'allowed-image-extensions', 'title' => __( 'Allowed Image Extensions', 'profilebuilder' ), 'default' => '.*', 'description' => __( 'Specify the extension(s) you want to limit to upload<br/>Example: .ext1,.ext2,.ext3<br/>If not specified, defaults to: .jpg,.jpeg,.gif,.png (.*)', 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'allowed-upload-extensions', 'title' => __( 'Allowed Upload Extensions', 'profilebuilder' ), 'default' => '.*', 'description' => __( 'Specify the extension(s) you want to limit to upload<br/>Example: .ext1,.ext2,.ext3<br/>If not specified, defaults to all WordPress allowed file extensions (.*)', 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'avatar-size', 'title' => __( 'Avatar Size', 'profilebuilder' ), 'default' => 100, 'description' => __( "Enter a value (between 20 and 200) for the size of the 'Avatar'<br/>If not specified, defaults to 100", 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'date-format', 'title' => __( 'Date-format', 'profilebuilder' ), 'default' => 'mm/dd/yy', 'description' => __( 'Specify the format of the date when using Datepicker<br/>Valid options: mm/dd/yy, mm/yy/dd, dd/yy/mm, dd/mm/yy, yy/dd/mm, yy/mm/dd<br/>If not specified, defaults to mm/dd/yy', 'profilebuilder' ) ),
        array( 'type' => 'textarea', 'slug' => 'terms-of-agreement', 'title' => __( 'Terms of Agreement', 'profilebuilder' ), 'description' => __( 'Enter a detailed description of the temrs of agreement for the user to read.<br/>Links can be inserted by using standard HTML syntax: &lt;a href="custom_url"&gt;custom_text&lt;/a&gt;', 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'options', 'title' => __( 'Options', 'profilebuilder' ), 'description' => __( "Enter a comma separated list of values<br/>This can be anything, as it is hidden from the user, but should not contain special characters or apostrophes", 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'labels', 'title' => __( 'Labels', 'profilebuilder' ), 'description' => __( "Enter a comma separated list of labels<br/>Visible for the user", 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'public-key', 'title' => __( 'Public Key', 'profilebuilder' ), 'description' => __( 'The public key from Google, <a href="http://www.google.com/recaptcha" target="_blank">www.google.com/recaptcha</a>', 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'private-key', 'title' => __( 'Private Key', 'profilebuilder' ), 'description' => __( 'The private key from Google, <a href="http://www.google.com/recaptcha" target="_blank">www.google.com/recaptcha</a>', 'profilebuilder' ) ),
        array( 'type' => 'checkbox', 'slug' => 'user-roles', 'title' => __( 'User Roles', 'profilebuilder' ), 'options' => $user_roles, 'description' => __( "Select which user roles to show to the user ( drag and drop to re-order )", 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'user-roles-sort-order', 'title' => __( 'User Roles Order', 'profilebuilder' ), 'description' => __( "Save the user role order from the user roles checkboxes", 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'default-value', 'title' => __( 'Default Value', 'profilebuilder' ), 'description' => __( "Default value of the field", 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'default-option', 'title' => __( 'Default Option', 'profilebuilder' ), 'description' => __( "Specify the option which should be selected by default", 'profilebuilder' ) ),
        array( 'type' => 'text', 'slug' => 'default-options', 'title' => __( 'Default Option(s)', 'profilebuilder' ), 'description' => __( "Specify the option which should be checked by default<br/>If there are multiple values, separate them with a ',' (comma)", 'profilebuilder' ) ),
        array( 'type' => 'textarea', 'slug' => 'default-content', 'title' => __( 'Default Content', 'profilebuilder' ), 'description' => __( "Default value of the textarea", 'profilebuilder' ) ),
        array( 'type' => 'select', 'slug' => 'required', 'title' => __( 'Required', 'profilebuilder' ), 'options' => array( 'No', 'Yes' ), 'default' => 'No', 'description' => __( 'Whether the field is required or not', 'profilebuilder' ) ),
        array( 'type' => 'select', 'slug' => 'overwrite-existing', 'title' => __( 'Overwrite Existing', 'profilebuilder' ), 'options' => array( 'No', 'Yes' ), 'default' => 'No', 'description' => __( "Selecting 'Yes' will add the field to the list, but will overwrite any other field in the database that has the same meta-name<br/>Use this at your own risk", 'profilebuilder' ) ),
    ) );
	
	// create the new submenu with the above options
	$args = array(
		'metabox_id' 	=> 'manage-fields',
		'metabox_title' => __( 'Field Properties', 'profilebuilder' ),
		'post_type' 	=> 'manage-fields',
		'meta_name' 	=> 'wppb_manage_fields',
		'meta_array' 	=> $fields,
		'context'		=> 'option'
		);
	new Wordpress_Creation_Kit_PB( $args );

    wppb_prepopulate_fields();

    // create the info side meta-box
    $args = array(
        'metabox_id' 	=> 'manage-fields-info',
        'metabox_title' => __( 'Registration & Edit Profile', 'profilebuilder' ),
        'post_type' 	=> 'manage-fields',
        'meta_name' 	=> 'wppb_manage_fields_info',
        'meta_array' 	=> '',
        'context'		=> 'option',
        'mb_context'    => 'side'
    );
    new Wordpress_Creation_Kit_PB( $args );
}
add_action( 'init', 'wppb_manage_fields_submenu', 10 );

/**
 * Function that prepopulates the manage fields list with the default fields of WP
 *
 * @since v.2.0
 *
 * @return void
 */
function wppb_prepopulate_fields(){
	$prepopulated_fields[] = array( 'field' => 'Default - Name (Heading)', 'field-title' => __( 'Name', 'profilebuilder' ), 'meta-name' => '',	'overwrite-existing' => 'No', 'id' => '1', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*',	'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'No' );
	$prepopulated_fields[] = array( 'field' => 'Default - Username', 'field-title' => __( 'Username', 'profilebuilder' ), 'meta-name' => '', 'overwrite-existing' => 'No', 'id' => '2', 'description' => __( 'Usernames cannot be changed.', 'profilebuilder' ), 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'Yes' );
	$prepopulated_fields[] = array( 'field' => 'Default - First Name', 'field-title' => __( 'First Name', 'profilebuilder' ), 'meta-name' => 'first_name', 'overwrite-existing' => 'No', 'id' => '3', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'No' );
	$prepopulated_fields[] = array( 'field' => 'Default - Last Name', 'field-title' => __( 'Last Name', 'profilebuilder' ), 'meta-name' => 'last_name', 'overwrite-existing' => 'No', 'id' => '4', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'No' );
	$prepopulated_fields[] = array( 'field' => 'Default - Nickname', 'field-title' => __( 'Nickname', 'profilebuilder' ), 'meta-name' => 'nickname', 'overwrite-existing' => 'No', 'id' => '5', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'Yes' );
	$prepopulated_fields[] = array( 'field' => 'Default - Display name publicly as', 'field-title' => __( 'Display name publicly as', 'profilebuilder' ), 'meta-name' => '', 'overwrite-existing' => 'No', 'id' => '6', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'No' );
	$prepopulated_fields[] = array( 'field' => 'Default - Contact Info (Heading)', 'field-title' => __( 'Contact Info', 'profilebuilder' ), 'meta-name' => '', 'overwrite-existing' => 'No', 'id' => '7', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'No' );
	$prepopulated_fields[] = array( 'field' => 'Default - E-mail', 'field-title' => __( 'E-mail', 'profilebuilder' ), 'meta-name' => '', 'overwrite-existing' => 'No', 'id' => '8', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'Yes' );
	$prepopulated_fields[] = array( 'field' => 'Default - Website', 'field-title' => __( 'Website', 'profilebuilder' ), 'meta-name' => '', 'overwrite-existing' => 'No', 'id' => '9', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'No' );
	
	// Default contact methods were removed in WP 3.6. A filter dictates contact methods.
	if ( apply_filters( 'wppb_remove_default_contact_methods', get_site_option( 'initial_db_version' ) < 23588 ) ){
		$prepopulated_fields[] = array( 'field' => 'Default - AIM', 'field-title' => __( 'AIM', 'profilebuilder' ), 'meta-name' => 'aim', 'overwrite-existing' => 'No', 'id' => '10', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'No' );
		$prepopulated_fields[] = array( 'field' => 'Default - Yahoo IM', 'field-title' => __( 'Yahoo IM', 'profilebuilder' ), 'meta-name' => 'yim', 'overwrite-existing' => 'No', 'id' => '11', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'No' );
		$prepopulated_fields[] = array( 'field' => 'Default - Jabber / Google Talk', 'field-title' => __( 'Jabber / Google Talk', 'profilebuilder' ), 'meta-name' => 'jabber', 'overwrite-existing' => 'No', 'id' => '12', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'No' );
	}
	
	$prepopulated_fields[] = array( 'field' => 'Default - About Yourself (Heading)', 'field-title' => __( 'About Yourself', 'profilebuilder' ), 'meta-name' => '', 'overwrite-existing' => 'No', 'id' => '13', 'description' => '', 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'No' );
	$prepopulated_fields[] = array( 'field' => 'Default - Biographical Info', 'field-title' => __( 'Biographical Info', 'profilebuilder' ), 'meta-name' => 'description', 'overwrite-existing' => 'No', 'id' => '14', 'description' => __( 'Share a little biographical information to fill out your profile. This may be shown publicly.', 'profilebuilder' ), 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'required' => 'No' );
	$prepopulated_fields[] = array( 'field' => 'Default - Password', 'field-title' => __( 'Password', 'profilebuilder' ), 'meta-name' => '', 'overwrite-existing' => 'No', 'id' => '15', 'description' => __( 'Type your password.', 'profilebuilder' ), 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'Yes' );
	$prepopulated_fields[] = array( 'field' => 'Default - Repeat Password', 'field-title' => __( 'Repeat Password', 'profilebuilder' ), 'meta-name' => '', 'overwrite-existing' => 'No', 'id' => '16', 'description' => __( 'Type your password again. ', 'profilebuilder' ), 'row-count' => '5', 'allowed-image-extensions' => '.*', 'allowed-upload-extensions' => '.*', 'avatar-size' => '100', 'date-format' => 'mm/dd/yy', 'terms-of-agreement' => '', 'options' => '', 'labels' => '', 'public-key' => '', 'private-key' => '', 'default-value' => '', 'default-option' => '', 'default-options' => '', 'default-content' => '', 'required' => 'Yes' );

	add_option ( 'wppb_manage_fields', apply_filters ( 'wppb_prepopulated_fields', $prepopulated_fields ) );
}

/**
 * Function that returns a unique meta-name
 *
 * @since v.2.0
 *
 * @return string
 */
function wppb_get_meta_name(){
	$id = 1;
	
	$wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );

	if ( ( $wppb_manage_fields === 'not_found' ) || ( empty( $wppb_manage_fields ) ) ){
		return 'custom_field_' . $id;
	}
    else{
        $meta_names = array();
		foreach( $wppb_manage_fields as $value ){
			if ( strpos( $value['meta-name'], 'custom_field' ) === 0 ){
                $meta_names[] = $value['meta-name'];
			}
		}

        if( !empty( $meta_names ) ){
            $meta_numbers = array();
            foreach( $meta_names as $meta_name ){
                $number = str_replace( 'custom_field', '', $meta_name );
                /* we should have an underscore present in custom_field_# so remove it */
                $number = str_replace( '_', '', $number );

                $meta_numbers[] = $number;
            }
            if( !empty( $meta_numbers ) ){
                rsort( $meta_numbers );
                $id = $meta_numbers[0]+1;
            }
        }

		return 'custom_field_' . $id;
	}
}


/**
 * Function that returns a unique, incremented ID
 *
 * @since v.2.0
 *
 * @return integer id
 */
function wppb_get_unique_id(){
    $id = 1;

    $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );
    if ( ( $wppb_manage_fields === 'not_found' ) || ( empty( $wppb_manage_fields ) ) ){
        return $id;
    }
    else{
        $ids_array = array();
        foreach( $wppb_manage_fields as $value ){
            $ids_array[] = $value['id'];
        }
        if( !empty( $ids_array ) ){
            rsort( $ids_array );
            $id = $ids_array[0] + 1;
        }
    }
    return $id;
}

/**
 * Function that checks to see if the id is unique when saving the new field
 *
 * @param array $values
 *
 * @return array
 */
function wppb_check_unique_id_on_saving( $values ) {
    $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );

    if( $wppb_manage_fields != 'not_found' ) {

        $ids_array = array();
        foreach( $wppb_manage_fields as $field ){
            $ids_array[] = $field['id'];
        }

        if( in_array( $values['id'], $ids_array ) ) {
            rsort( $ids_array );
            $values['id'] = $ids_array[0] + 1;
        }

    }

    return $values;
}
add_filter( 'wck_add_meta_filter_values_wppb_manage_fields', 'wppb_check_unique_id_on_saving' );


function wppb_return_unique_field_list( $only_default_fields = false ){
	
	$unique_field_list[] = 'Default - Name (Heading)';
	$unique_field_list[] = 'Default - Contact Info (Heading)';
	$unique_field_list[] = 'Default - About Yourself (Heading)';
	$unique_field_list[] = 'Default - Username';
	$unique_field_list[] = 'Default - First Name';
	$unique_field_list[] = 'Default - Last Name';
	$unique_field_list[] = 'Default - Nickname';
	$unique_field_list[] = 'Default - E-mail';
	$unique_field_list[] = 'Default - Website';

	// Default contact methods were removed in WP 3.6. A filter dictates contact methods.
	if ( apply_filters( 'wppb_remove_default_contact_methods', get_site_option( 'initial_db_version' ) < 23588 ) ){
		$unique_field_list[] = 'Default - AIM';
		$unique_field_list[] = 'Default - Yahoo IM';
		$unique_field_list[] = 'Default - Jabber / Google Talk';
	}
	
	$unique_field_list[] = 'Default - Password';
	$unique_field_list[] = 'Default - Repeat Password';
	$unique_field_list[] = 'Default - Biographical Info';
	$unique_field_list[] = 'Default - Display name publicly as';
    if( !$only_default_fields ){
	    $unique_field_list[] = 'Avatar';
	    $unique_field_list[] = 'reCAPTCHA';
        $unique_field_list[] = 'Select (User Role)';
    }

	return 	apply_filters ( 'wppb_unique_field_list', $unique_field_list );
}


/**
 * Function that checks several things when adding/editing the fields
 *
 * @since v.2.0
 *
 * @param string $message - the message to be displayed
 * @param array $fields - the added fields
 * @param array $required_fields
 * @param string $meta - the meta-name of the option
 * @param string $values - The values entered for each option
 * @param integer $post_id
 * @return boolean
 */
function wppb_check_field_on_edit_add( $message, $fields, $required_fields, $meta_name, $posted_values, $post_id ){
	global $wpdb;
	
	if ( $meta_name == 'wppb_manage_fields' ){
	
		// check for a valid field-type (fallback)
		if ( $posted_values['field'] == '' )
			$message .= __( "You must select a field\n", 'profilebuilder' );
		// END check for a valid field-type (fallback)
		
		$unique_field_list = wppb_return_unique_field_list();
		$all_fields = get_option ( $meta_name, 'not_set' );
		
		// check if the unique fields are only added once
		if( $all_fields != 'not_set' ){
			foreach( $all_fields as $field ){
				if ( ( in_array ( $posted_values['field'], $unique_field_list ) ) && ( $posted_values['field'] == $field['field'] ) && ( $posted_values['id'] != $field['id'] ) ){
					$message .= __( "Please choose a different field type as this one already exists in your form (must be unique)\n", 'profilebuilder' );
					break;
				}
			}
		}
		// END check if the unique fields are only added once
		
		// check for avatar size
		if ( $posted_values['field'] == 'Avatar' ){
			if ( is_numeric( $posted_values['avatar-size'] ) ){
				if ( ( $posted_values['avatar-size'] < 20 ) || ( $posted_values['avatar-size'] > 200 ) )
					$message .= __( "The entered avatar size is not between 20 and 200\n", 'profilebuilder' );
			
			}else
				$message .= __( "The entered avatar size is not numerical\n", 'profilebuilder' );

		}
		// END check for avatar size
		
		// check for correct row value
		if ( ( $posted_values['field'] == 'Default - Biographical Info' ) || ( $posted_values['field'] == 'Textarea' ) ){
			if ( !is_numeric( $posted_values['row-count'] ) )
				$message .= __( "The entered row number is not numerical\n", 'profilebuilder' );
				
			elseif ( trim( $posted_values['row-count'] ) == '' )
				$message .= __( "You must enter a value for the row number\n", 'profilebuilder' );
		}
		// END check for correct row value
		

		// check for the public and private keys
		if ( $posted_values['field'] == 'reCAPTCHA'){
			if ( trim( $posted_values['public-key'] ) == '' )
				$message .= __( "You must enter the public key\n", 'profilebuilder' );
			if ( trim( $posted_values['private-key'] ) == '' )
				$message .= __( "You must enter the private key\n", 'profilebuilder' );
		}
		// END check for the public and private keys
		
		// check for the correct the date-format
		if ( $posted_values['field'] == 'Datepicker' ){
			$date_format = strtolower( $posted_values['date-format'] );			
			if ( ( trim( $date_format ) != 'mm/dd/yy' ) && ( trim( $date_format ) != 'mm/yy/dd' ) && ( trim( $date_format ) != 'dd/yy/mm' ) && ( trim( $date_format ) != 'dd/mm/yy' ) && ( trim( $date_format ) != 'yy/dd/mm' ) && ( trim( $date_format ) != 'yy/mm/dd' ) )
				$message .= __( "The entered value for the Datepicker is not a valid date-format\n", 'profilebuilder' );
			
			elseif ( trim( $date_format ) == '' )
				$message .= __( "You must enter a value for the date-format\n", 'profilebuilder' );
		}
		// END check for the correct the date-format	
		
		//check for empty meta-name and duplicate meta-name
		if ( $posted_values['overwrite-existing'] == 'No' ){
            $skip_check_for_fields = wppb_return_unique_field_list(true);
            $skip_check_for_fields = apply_filters ( 'wppb_skip_check_for_fields', $skip_check_for_fields );
		
			if ( !in_array( trim( $posted_values['field'] ), $skip_check_for_fields ) ){
				$unique_meta_name_list = array( 'first_name', 'last_name', 'nickname', 'description' );

                //check to see if meta-name is empty
                $skip_empty_check_for_fields = array('Heading', 'Select (User Role)', 'reCAPTCHA');

                if( !in_array( $posted_values['field'], $skip_empty_check_for_fields ) && empty( $posted_values['meta-name'] ) ) {
                    $message .= __( "The meta-name cannot be empty\n", 'profilebuilder' );
                }

				// Default contact methods were removed in WP 3.6. A filter dictates contact methods.
				if ( apply_filters( 'wppb_remove_default_contact_methods', get_site_option( 'initial_db_version' ) < 23588 ) ){
					$unique_meta_name_list[] = 'aim';
					$unique_meta_name_list[] = 'yim';
					$unique_meta_name_list[] = 'jabber';
				}
				
				// if the desired meta-name is one of the following, automatically give an error
				if ( in_array( trim( $posted_values['meta-name'] ), apply_filters ( 'wppb_unique_meta_name_list', $unique_meta_name_list ) ) )
					$message .= __( "That meta-name is already in use\n", 'profilebuilder' );
				
				else{
					$found_in_custom_fields = false;
					
					if( $all_fields != 'not_set' )
						foreach( $all_fields as $field ){
							if ( $posted_values['meta-name'] != '' && ( $field['meta-name'] == $posted_values['meta-name'] ) && ( $field['id'] != $posted_values['id'] ) ){
								$message .= __( "That meta-name is already in use\n", 'profilebuilder' );
								$found_in_custom_fields = true;
							
							}elseif ( ( $field['meta-name'] == $posted_values['meta-name'] ) && ( $field['id'] == $posted_values['id'] ) )
								$found_in_custom_fields = true;
						}
					
					if ( $found_in_custom_fields === false ){
						$found_meta_name = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->usermeta WHERE meta_key = %s", $posted_values['meta-name'] ) );
						if ( $found_meta_name != null )
							$message .= __( "That meta-name is already in use\n", 'profilebuilder' );
					}
				}
			}
		}
		//END check duplicate meta-name
		
		// check for valid default option (checkbox, select, radio)
		if ( ( $posted_values['field'] == 'Checkbox' ) || ( $posted_values['field'] == 'Select (Multiple)' ) ) {
			$options = array_map( 'trim', explode( ',', $posted_values['options'] ) );
			$default_options = ( ( trim( $posted_values['default-options'] ) == '' ) ? array() : array_map( 'trim', explode( ',', $posted_values['default-options'] ) ) );

			/* echo "<script>console.log(  Posted options: " . print_r($options, true) . "' );</script>";
			echo "<script>console.log(  Default options: " . print_r($default_options, true) . "' );</script>"; */
			
			$not_found = '';
			foreach ( $default_options as $key => $value ){
				if ( !in_array( $value, $options ) )
					$not_found .= $value . ', ';
			}
		
			if ( $not_found != '' )
				$message .= sprintf( __( "The following option(s) did not coincide with the ones in the options list: %s\n", 'profilebuilder' ), trim( $not_found, ', ' ) );
			
		}elseif ( ( $posted_values['field'] == 'Radio' ) || ( $posted_values['field'] == 'Select' ) ){
			if ( ( trim( $posted_values['default-option'] ) != '' )  && ( !in_array( $posted_values['default-option'], array_map( 'trim', explode( ',', $posted_values['options'] ) ) ) ) )
				$message .= sprintf( __( "The following option did not coincide with the ones in the options list: %s\n", 'profilebuilder' ), $posted_values['default-option'] );
		}
		// END check for valid default option (checkbox, select, radio)

        // check to see if any user role is selected (user-role field)
        if( $posted_values['field'] == 'Select (User Role)' ) {
            if( empty( $posted_values['user-roles'] ) ) {
                $message .= __( "Please select at least one user role\n", 'profilebuilder' );
            }
        }
        // END check to see if Administrator user role has been selected (user-role field)

        $message = apply_filters( 'wppb_check_extra_manage_fields', $message, $posted_values );

	}elseif ( ( $meta_name == 'wppb_rf_fields' ) || ( $meta_name == 'wppb_epf_fields' ) ){
		if ( $posted_values['field'] == '' ){
			$message .= __( "You must select a field\n", 'profilebuilder' );
			
		}else{
			$fields_so_far = get_post_meta ( $post_id, $meta_name, true );
			
			foreach ( $fields_so_far as $key => $value ){
				if ( $value['field'] == $posted_values['field'] )
					$message .= __( "That field is already added in this form\n", 'profilebuilder' );
			}
		}
	}
	return $message;
}
add_filter( 'wck_extra_message', 'wppb_check_field_on_edit_add', 10, 6 );


/**
 * Function that calls the wppb_hide_properties_for_already_added_fields after a field-update
 *
 * @since v.2.0
 *
 * @param void
 *
 * @return string
 */
function wppb_manage_fields_after_refresh_list( $id ){
	echo "<script type=\"text/javascript\">wppb_hide_properties_for_already_added_fields( '#container_wppb_manage_fields' );</script>";
}
add_action( "wck_refresh_list_wppb_manage_fields", "wppb_manage_fields_after_refresh_list" );
add_action( "wck_refresh_entry_wppb_manage_fields", "wppb_manage_fields_after_refresh_list" );


/**
 * Function that calls the wppb_hide_all
 *
 * @since v.2.0
 *
 * @param void
 *
 * @return string
 */
function wppb_hide_all_after_add( $id ){
	echo "<script type=\"text/javascript\">wppb_hide_all( '#wppb_manage_fields' );</script>";
}
add_action("wck_ajax_add_form_wppb_manage_fields", "wppb_hide_all_after_add" );

/**
 * Function that modifies the table header in Manage Fields to add Field Name, Field Type, Meta Key, Required
 *
 * @since v.2.0
 *
 * @param $list, $id
 *
 * @return string
 */
function wppb_manage_fields_header( $list_header ){
	return '<thead><tr><th class="wck-number">#</th><th class="wck-content">'. __( '<pre>Title</pre><pre>Type</pre><pre>Meta Name</pre><pre class="wppb-mb-head-required">Required</pre>', 'profilebuilder' ) .'</th><th class="wck-edit">'. __( 'Edit', 'profilebuilder' ) .'</th><th class="wck-delete">'. __( 'Delete', 'profilebuilder' ) .'</th></tr></thead>';
}
add_action( 'wck_metabox_content_header_wppb_manage_fields', 'wppb_manage_fields_header' );

/**
 * Add contextual help to the side of manage fields for the shortcodes
 *
 * @since v.2.0
 *
 * @param $hook
 *
 * @return string
 */
function wppb_add_content_before_manage_fields(){
?>
   <p><?php _e('Use these shortcodes on the pages you want the forms to be displayed:', 'profilebuilder'); ?></p>
   <ul>
        <li><strong class="nowrap">[wppb-register]</strong></li>
        <li><strong class="nowrap">[wppb-edit-profile]</strong></li>
        <li><strong class="nowrap">[wppb-register role="author"]</strong></li>
   </ul>
   <p><?php _e("If you're interested in displaying different fields in the registration and edit profile forms, please use the Multiple Registration & Edit Profile Forms Addon.", 'profilebuilder'); ?></p>
<?php
}
add_action('wck_metabox_content_wppb_manage_fields_info', 'wppb_add_content_before_manage_fields');


/**
 * Function that calls the wppb_edit_form_properties
 *
 * @since v.2.0
 *
 * @param void
 *
 * @return string
 */
function wppb_remove_properties_from_added_form( $meta_name, $id, $element_id ){
    if ( ( $meta_name == 'wppb_epf_fields' ) || ( $meta_name == 'wppb_rf_fields' ) )
        echo "<script type=\"text/javascript\">wppb_disable_delete_on_default_mandatory_fields();</script>";

    if ( $meta_name == 'wppb_manage_fields' )
        echo "<script type=\"text/javascript\">wppb_edit_form_properties( '#container_wppb_manage_fields', 'update_container_wppb_manage_fields_".$element_id."' );</script>";
}
add_action("wck_after_adding_form", "wppb_remove_properties_from_added_form", 10, 3);

/*
 * WPML Support for dynamic strings in Profile Builder. Tags: WPML, fields, translate
 */
add_filter( 'update_option_wppb_manage_fields', 'wppb_wpml_compat_with_fields', 10, 2 );
function wppb_wpml_compat_with_fields( $oldvalue, $_newvalue ){
    if ( is_array( $_newvalue ) ){
        foreach ( $_newvalue as $field ){
            $field_title = $field['field-title'];
            $field_description = $field['description'];
            if (function_exists('icl_register_string')){
                icl_register_string('plugin profile-builder-pro', 'custom_field_'.$field['id'].'_title_translation' , $field_title);
                icl_register_string('plugin profile-builder-pro', 'custom_field_'.$field['id'].'_description_translation', $field_description);
            }
        }
    }
}