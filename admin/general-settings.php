<?php
/**
 * Function that creates the "General Settings" submenu page
 *
 * @since v.2.0
 *
 * @return void
 */
function wppb_register_general_settings_submenu_page() {
	add_submenu_page( 'profile-builder', __( 'General Settings', 'profilebuilder' ), __( 'General Settings', 'profilebuilder' ), 'manage_options', 'profile-builder-general-settings', 'wppb_general_settings_content' ); 
}
add_action( 'admin_menu', 'wppb_register_general_settings_submenu_page', 3 );


function wppb_generate_default_settings_defaults(){
	$wppb_general_settings = get_option( 'wppb_general_settings', 'not_found' );
	
	if ( $wppb_general_settings == 'not_found' )
		update_option( 'wppb_general_settings', array( 'extraFieldsLayout' => 'default', 'emailConfirmation' => 'no', 'activationLandingPage' => '', 'adminApproval' => 'no', 'loginWith' => 'usernameemail' ) );
}


/**
 * Function that adds content to the "General Settings" submenu page
 *
 * @since v.2.0
 *
 * @return string
 */
function wppb_general_settings_content() {
	wppb_generate_default_settings_defaults();
?>	
	<div class="wrap wppb-wrap">
	<form method="post" action="options.php#general-settings">
	<?php $wppb_generalSettings = get_option( 'wppb_general_settings' ); ?>
	<?php settings_fields( 'wppb_general_settings' ); ?>

	<h2><?php _e( 'General Settings', 'profilebuilder' ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row">
				<?php _e( "Load Profile Builder's own CSS file in the front-end:", "profilebuilder" ); ?>
			</th>
			<td>
				<label><input type="checkbox" name="wppb_general_settings[extraFieldsLayout]"<?php echo ( ( isset( $wppb_generalSettings['extraFieldsLayout'] ) && ( $wppb_generalSettings['extraFieldsLayout'] == 'default' ) ) ? ' checked' : '' ); ?> value="default" class="wppb-select"><?php _e( 'Yes', 'profilebuilder' ); ?></label>
				<ul>
					<li class="description"><?php printf( __( 'You can find the default file here: %1$s', 'profilebuilder' ), '<a href="'.dirname( plugin_dir_url( __FILE__ ) ).'/assets/css/style-front-end.css" target="_blank">'.dirname( dirname( plugin_basename( __FILE__ ) ) ).'\assets\css\style-front-end.css</a>' ); ?></li>
				</ul>
			</td>
		</tr>
		<?php
		if ( !is_multisite() ){
		?>
		<tr>
			<th scope="row">
				<?php _e( '"Email Confirmation" Activated:', 'profilebuilder' );?>
			</th>
			<td>
				<select name="wppb_general_settings[emailConfirmation]" class="wppb-select" id="wppb_settings_email_confirmation" onchange="wppb_display_page_select(this.value)">
					<option value="yes" <?php if ( $wppb_generalSettings['emailConfirmation'] == 'yes' ) echo 'selected'; ?>><?php _e( 'Yes', 'profilebuilder' ); ?></option>
					<option value="no" <?php if ( $wppb_generalSettings['emailConfirmation'] == 'no' ) echo 'selected'; ?>><?php _e( 'No', 'profilebuilder' ); ?></option>
				</select>
				<ul>
				<li class="description"><?php _e( 'On single-site installations this works with front-end forms only. Recommended to redirect WP default registration to a Profile Builder one using "Custom Redirects" addon.', 'profilebuilder' ); ?></li>
				<li class="description"><?php _e( 'The "Email Confirmation" feature is active (by default) on WPMU installations.', 'profilebuilder' ); ?></li>
				<?php if ( is_multisite() || ( $wppb_generalSettings['emailConfirmation'] == 'yes' ) ) ?>
					<li class="description dynamic1"><?php printf( __( 'You can find a list of unconfirmed email addresses %1$sUsers > All Users > Email Confirmation%2$s.', 'profilebuilder' ), '<a href="'.get_bloginfo( 'url' ).'/wp-admin/users.php?page=unconfirmed_emails">', '</a>' )?></li>
				</ul>
			</td>
		</tr>
		<?php
		}else{
			echo '<input type="hidden" id="wppb_general_settings_hidden" value="multisite"/>';
		}
		?>

		<tr id="wppb-settings-activation-page">
			<th scope="row">
				<?php _e( '"Email Confirmation" Landing Page:', 'profilebuilder' ); ?>
			</th>
			<td>
				<select name="wppb_general_settings[activationLandingPage]" class="wppb-select">
					<option value="" <?php if ( empty( $wppb_generalSettings['emailConfirmation'] ) ) echo 'selected'; ?>></option>
					<optgroup label="<?php _e( 'Existing Pages', 'profilebuilder' ); ?>">
					<?php
						$pages = get_pages( apply_filters( 'wppb_page_args_filter', array( 'sort_order' => 'ASC', 'sort_column' => 'post_title', 'post_type' => 'page', 'post_status' => array( 'publish' ) ) ) );
						
						foreach ( $pages as $key => $value ){
							echo '<option value="'.$value->ID.'"';
							if ( $wppb_generalSettings['activationLandingPage'] == $value->ID )
								echo ' selected';

							echo '>' . $value->post_title . '</option>';
						}
					?>
					</optgroup>
				</select>
				<p class="description">
					<?php _e( 'Specify the page where the users will be directed when confirming the email account. This page can differ from the register page(s) and can be changed at any time. If none selected, a simple confirmation page will be displayed for the user.', 'profilebuilder' ); ?>
				</p>
			</td>
		</tr>


	<?php
	if ( file_exists( WPPB_PLUGIN_DIR.'/features/admin-approval/admin-approval.php' ) ){
	?>
		<tr>
			<th scope="row">
				<?php _e( '"Admin Approval" Activated:', 'profilebuilder' ); ?>
			</th>
			<td>
				<select id="adminApprovalSelect" name="wppb_general_settings[adminApproval]" class="wppb-select" onchange="wppb_display_page_select_aa(this.value)">
					<option value="yes" <?php if( !empty( $wppb_generalSettings['adminApproval'] ) && $wppb_generalSettings['adminApproval'] == 'yes' ) echo 'selected'; ?>><?php _e( 'Yes', 'profilebuilder' ); ?></option>
					<option value="no" <?php if( !empty( $wppb_generalSettings['adminApproval'] ) && $wppb_generalSettings['adminApproval'] == 'no' ) echo 'selected'; ?>><?php _e( 'No', 'profilebuilder' ); ?></option>
				</select>
				<ul>
					<li class="description dynamic2"><?php printf( __( 'You can find a list of users at %1$sUsers > All Users > Admin Approval%2$s.', 'profilebuilder' ), '<a href="'.get_bloginfo( 'url' ).'/wp-admin/users.php?page=admin_approval&orderby=registered&order=desc">', '</a>' )?></li>
				<ul>
			</td>
		</tr>
	
	<?php } ?>

	<?php
	if ( PROFILE_BUILDER == 'Profile Builder Free' ) {
	?>
		<tr>
			<th scope="row">
				<?php _e( '"Admin Approval" Feature:', 'profilebuilder' ); ?>
			</th>
			<td>
				<p><em>	<?php printf( __( 'You decide who is a user on your website. Get notified via email or approve multiple users at once from the WordPress UI. Enable Admin Approval by upgrading to %1$sHobbyist or PRO versions%2$s.', 'profilebuilder' ),'<a href="http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wpbackend&utm_medium=clientsite&utm_content=general-settings-link&utm_campaign=PBFree">', '</a>' )?></em></p>
			</td>
		</tr>
	<?php } ?>

		<tr>
			<th scope="row">
				<?php _e( 'Allow Users to Log in With:', 'profilebuilder' ); ?>
			</th>
			<td>
				<select name="wppb_general_settings[loginWith]" class="wppb-select">
					<option value="usernameemail" <?php if ( $wppb_generalSettings['loginWith'] == 'usernameemail' ) echo 'selected'; ?>><?php _e( 'Username and Email', 'profilebuilder' ); ?></option>
					<option value="username" <?php if ( $wppb_generalSettings['loginWith'] == 'username' ) echo 'selected'; ?>><?php _e( 'Username', 'profilebuilder' ); ?></option>
					<option value="email" <?php if ( $wppb_generalSettings['loginWith'] == 'email' ) echo 'selected'; ?>><?php _e( 'Email', 'profilebuilder' ); ?></option>
				</select>
				<ul>
					<li class="description"><?php _e( '"Username and Email" - users can Log In with both Username and Email.', 'profilebuilder' ); ?></li>
					<li class="description"><?php _e( '"Username" - users can Log In only with Username.', 'profilebuilder' ); ?></li>
					<li class="description"><?php _e( '"Email" - users can Log In only with Email.', 'profilebuilder' ); ?></li>
				</ul>
			</td>
		</tr>

        <tr>
            <th scope="row">
                <?php _e( 'Minimum Password Length:', 'profilebuilder' ); ?>
            </th>
            <td>
                <input type="text" name="wppb_general_settings[minimum_password_length]" class="wppb-text" value="<?php if( !empty( $wppb_generalSettings['minimum_password_length'] ) ) echo $wppb_generalSettings['minimum_password_length']; ?>"/>
                <ul>
                    <li class="description"><?php _e( 'Enter the minimum characters the password should have. Leave empty for no minimum limit', 'profilebuilder' ); ?> </li>
                </ul>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <?php _e( 'Minimum Password Strength:', 'profilebuilder' ); ?>
            </th>
            <td>
                <select name="wppb_general_settings[minimum_password_strength]" class="wppb-select">
                    <option value=""><?php _e( 'Disabled', 'profilebuilder' ); ?></option>
                    <option value="short" <?php if ( !empty($wppb_generalSettings['minimum_password_strength']) && $wppb_generalSettings['minimum_password_strength'] == 'short' ) echo 'selected'; ?>><?php _e( 'Very weak', 'profilebuilder' ); ?></option>
                    <option value="bad" <?php if ( !empty($wppb_generalSettings['minimum_password_strength']) && $wppb_generalSettings['minimum_password_strength'] == 'bad' ) echo 'selected'; ?>><?php _e( 'Weak', 'profilebuilder' ); ?></option>
                    <option value="good" <?php if ( !empty($wppb_generalSettings['minimum_password_strength']) && $wppb_generalSettings['minimum_password_strength'] == 'good' ) echo 'selected'; ?>><?php _e( 'Medium', 'profilebuilder' ); ?></option>
                    <option value="strong" <?php if ( !empty($wppb_generalSettings['minimum_password_strength']) && $wppb_generalSettings['minimum_password_strength'] == 'strong' ) echo 'selected'; ?>><?php _e( 'Strong', 'profilebuilder' ); ?></option>
                </select>
            </td>
        </tr>

        <?php do_action( 'wppb_extra_general_settings', $wppb_generalSettings ); ?>
	</table>
		
	
	
	<input type="hidden" name="action" value="update" />
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" /></p>
</form>
</div>
	
<?php
}


/*
 * Function that sanitizes the general settings
 *
 * @param array $wppb_generalSettings
 *
 * @since v.2.0.7
 */
function wppb_general_settings_sanitize( $wppb_generalSettings ) {

    $wppb_generalSettings = apply_filters( 'wppb_general_settings_sanitize_extra', $wppb_generalSettings );

    return $wppb_generalSettings;
}


/*
 * Function that pushes settings errors to the user
 *
 * @since v.2.0.7
 */
function wppb_general_settings_admin_notices() {
    settings_errors( 'wppb_general_settings' );
}
add_action( 'admin_notices', 'wppb_general_settings_admin_notices' );