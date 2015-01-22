<?php
 /* @param string $meta Meta name.	 
 * @param array $details Contains the details for the field.	 
 * @param string $value Contains input value;
 * @param string $context Context where the function is used. Depending on it some actions are preformed.;
 * @return string $element input element html string. */

/* since the slug below is generated dinamically from other elements we need to determine here if we have a slug or not and not let the wck_generate_slug() function do that */
if( !empty( $details['slug'] ) )
    $slug_from = $details['slug'];
else
    $slug_from = $details['title'];

/* define id's for input and info div */
$upload_input_id = str_replace( '-', '_', Wordpress_Creation_Kit_PB::wck_generate_slug( $meta . $slug_from ) );
$upload_info_div_id = str_replace( '-', '_', Wordpress_Creation_Kit_PB::wck_generate_slug( $meta .'_info_container_'. $slug_from ) );

/* hidden input that will hold the attachment id */
$element .= '<input id="'. esc_attr( $upload_input_id ) .'" type="hidden" size="36" name="'. $single_prefix . esc_attr( Wordpress_Creation_Kit_PB::wck_generate_slug( $details['title'], $details ) ) .'" value="'. $value .'" class="mb-text-input mb-field"/>';

$thumbnail = '';
$file_name = '';
$file_type = '';
/* container for the image preview (or file ico) and name and file type */
if( !empty ( $value ) ){
	$file_src = wp_get_attachment_url($value);
	$thumbnail = wp_get_attachment_image( $value, array( 80, 60 ), true );
	$file_name = get_the_title( $value );
	
	if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $value ), $matches ) )
		$file_type = esc_html( strtoupper( $matches[1] ) );
	else
		$file_type = strtoupper( str_replace( 'image/', '', get_post_mime_type( $value ) ) );
}
$element .= '<div id="'. esc_attr( $upload_info_div_id ) .'" class="upload-field-details">'. $thumbnail .'<p><span class="file-name">'. $file_name .'</span><span class="file-type">'. $file_type . '</span>';
if( !empty ( $value ) )
	$element .= '<span class="wck-remove-upload">'.__( 'Remove', 'wck' ).'</span>';
$element .= '</p></div>';
/* the upload link. we send through get the hidden input id, details div id and meta name */
if( !empty( $details['attach_to_post'] ) ){
	$attach_to_post = 'post_id='. $post_id .'&amp;';
}else {
	$attach_to_post = '';
}
if( empty( $var_prefix ) )
	$var_prefix = '';
if( empty( $edit_class ) )	
	$edit_class = '';
	
$media_upload_url = 'media-upload.php?'.$attach_to_post.'type=file&amp;mb_type='. $var_prefix  . esc_js(strtolower( $upload_input_id ) ).'&amp;mb_info_div='.$var_prefix  . esc_js(strtolower( $upload_info_div_id ) ).'&amp;meta_name='.$meta.'&amp;TB_iframe=1';			

$media_upload_url = admin_url( $media_upload_url );
	
$element .= '<a id="upload_'. esc_attr(Wordpress_Creation_Kit_PB::wck_generate_slug( $details['title'], $details ) ) .'_button" class="button" onclick="tb_show(\'\', \''.$media_upload_url.'\');">'. __( 'Upload ', 'wck' ) . $details['title'] .' </a>';

/* add js global var for the hidden input, and info container div */
$element .= '<script type="text/javascript">';				
	$element .= 'window.'. $var_prefix . strtolower( $upload_input_id ) .' = jQuery(\''.$edit_class.'#'. $upload_input_id.'\');';
	$element .= 'window.'. $var_prefix . strtolower( $upload_info_div_id ) .' = jQuery(\''.$edit_class.'#'. $upload_info_div_id.'\');';
$element .= '</script>';
?>