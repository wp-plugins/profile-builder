<?php
/**
 * Function that creates the "Basic Information" submenu page
 *
 * @since v.2.0
 *
 * @return void
 */
function wppb_register_basic_info_submenu_page() {
	add_submenu_page( 'profile-builder', __( 'Basic Information', 'profilebuilder' ), __( 'Basic Information', 'profilebuilder' ), 'manage_options', 'profile-builder-basic-info', 'wppb_basic_info_content' ); 
}
add_action( 'admin_menu', 'wppb_register_basic_info_submenu_page', 2 );

/**
 * Function that adds content to the "Basic Information" submenu page
 *
 * @since v.2.0
 *
 * @return string
 */
function wppb_basic_info_content() {
	
	$version = 'Free';
	$version = ( ( PROFILE_BUILDER == 'Profile Builder Pro' ) ? 'Pro' : $version );
	$version = ( ( PROFILE_BUILDER == 'Profile Builder Hobbyist' ) ? 'Hobbyist' : $version );

?>
	<div class="wrap wppb-wrap wppb-info-wrap">
		<div class="wppb-badge <?php echo $version; ?>"><?php printf( __( 'Version %s' ), PROFILE_BUILDER_VERSION ); ?></div>
		<h1><?php printf( __( '<strong>Profile Builder </strong>' . $version . ' <small>v.</small>%s', 'profilebuilder' ), PROFILE_BUILDER_VERSION ); ?></h1>
		<p class="wppb-info-text"><?php printf( __( 'The best way to add front-end registration, edit profile and login forms.', 'profilebuilder' ) ); ?></p>
		<hr />
		<h2 class="wppb-callout"><?php _e( 'For Modern User Interaction', 'profilebuilder' ); ?></h2>
		<div class="wppb-row wppb-3-col">
			<div>
				<h3><?php _e( 'Login', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'Friction-less login using <strong class="nowrap">[wppb-login]</strong> shortcode or a widget.', 'profilebuilder' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Registration', 'profilebuilder'  ); ?></h3>
				<p><?php _e( 'Beautiful registration forms fully customizable using the <strong class="nowrap">[wppb-register]</strong> shortcode.', 'profilebuilder' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Edit Profile', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'Straight forward edit profile forms using <strong class="nowrap">[wppb-edit-profile]</strong> shortcode.', 'profilebuilder' ); ?></p>
			</div>
		</div>
		<?php ob_start(); ?>
		<hr/>
		<div>
			<h3><?php _e( 'Extra Features', 'profilebuilder' );?></h3>
			<p><?php _e( 'Features that give you more control over your users, increased security and help you fight user registration spam.', 'profilebuilder' ); ?></p>
			<p><a href="admin.php?page=profile-builder-general-settings" class="button"><?php _e( 'Enable extra features', 'profilebuilder' ); ?></a></p>
		</div>
		<div class="wppb-row wppb-3-col">
			<div>
				<h3><?php _e( 'Recover Password', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'Allow users to recover their password in the front-end using the [wppb-recover-password].', 'profilebuilder' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Admin Approval (*)', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'You decide who is a user on your website. Get notified via email or approve multiple users at once from the WordPress UI.', 'profilebuilder' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Email Confirmation', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'Make sure users sign up with genuine emails. On registration users will receive a notification to confirm their email address.', 'profilebuilder' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Minimum Password Length and Strength Meter', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'Eliminate weak passwords altogether by setting a minimum password length and enforcing a certain password strength.', 'profilebuilder' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Login with Email or Username', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'Allow users to log in with their email or username when accessing your site.', 'profilebuilder' ); ?></p>
			</div>
		</div>

		<?php
		// Output here the Extra Features html for the Free version
		$extra_features_html = ob_get_contents();
		ob_end_clean();
		if ( $version == 'Free' ) echo $extra_features_html; ?>

		<hr/>
		<div class="wppb-row wppb-2-col">
			<div>
				<h3><?php _e( 'Customize Your Forms The Way You Want (*)', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'With Extra Profile Fields you can create the exact registration form your project needs.', 'profilebuilder' ); ?></p>
				<?php if ($version == 'Free'){ ?>
					<p><a href="http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wpbackend&utm_medium=clientsite&utm_content=basicinfo-extrafields&utm_campaign=PBFree" class="wppb-button-free"><?php _e( 'Extra Profile Fields are available in Hobbyist or PRO versions', 'profilebuilder' ); ?></a></p>
				<?php } else {?>
					<p><a href="admin.php?page=manage-fields" class="button"><?php _e( 'Get started with extra fields', 'profilebuilder' ); ?></a></p>
				<?php } ?>
				<ul>
					<li><?php _e( 'Avatar Upload', 'profilebuilder' ); ?></li>
					<li><?php _e( 'Generic Uploads', 'profilebuilder' ); ?></li>
					<li><?php _e( 'Agree To Terms Checkbox', 'profilebuilder' ); ?></li>
					<li><?php _e( 'Datepicker', 'profilebuilder' ); ?> </li>
					<li><?php _e( 'reCAPTCHA', 'profilebuilder' ); ?></li>
					<li><?php _e( 'Country Select', 'profilebuilder' ); ?></li>
					<li><?php _e( 'Timezone Select', 'profilebuilder' ); ?></li>
					<li><?php _e( 'Input / Hidden Input', 'profilebuilder' ); ?></li>
					<li><?php _e( 'Checkbox', 'profilebuilder' ); ?></li>
					<li><?php _e( 'Select', 'profilebuilder' ); ?></li>
					<li><?php _e( 'Radio Buttons', 'profilebuilder' ); ?></li>
					<li><?php _e( 'Textarea', 'profilebuilder' ); ?></li>
				</ul>
			</div>
			<div>
				<img src="<?php echo WPPB_PLUGIN_URL; ?>assets/images/pb_fields.png" alt="Profile Builder Extra Fields" class="wppb-fields-image" />
			</div>
		</div>
		<hr/>
		<div> 
			<h3><?php _e( 'Powerful Modules (**)', 'profilebuilder' );?></h3>
			<p><?php _e( 'Everything you will need to manage your users is probably already available using the Pro Modules.', 'profilebuilder' ); ?></p>
            <?php if( file_exists ( WPPB_PLUGIN_DIR.'/modules/modules.php' ) ): ?>
			    <p><a href="admin.php?page=profile-builder-modules" class="button"><?php _e( 'Enable your modules', 'profilebuilder' ); ?></a></p>
            <?php endif; ?>
			<?php if ($version == 'Free'){ ?>
				<p><a href="http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wpbackend&utm_medium=clientsite&utm_content=basicinfo-modules&utm_campaign=PBFree" class="wppb-button-free"><?php _e( 'Find out more about PRO Modules', 'profilebuilder' ); ?></a></p>
			<?php }?>
		</div>
		<div class="wppb-row wppb-3-col">
			<div>
				<h3><?php _e( 'User Listing', 'profilebuilder' ); ?></h3>
				<?php if ($version == 'Free'): ?>
				<p><?php _e( 'Easy to edit templates for listing your website users as well as creating single user pages. Shortcode based, offering many options to customize your listings.', 'profilebuilder' ); ?></p>
				<?php else : ?>
				<p><?php _e( 'To create a page containing the users registered to this current site/blog, insert the following shortcode in a page of your chosing: <strong class="nowrap">[wppb-list-users]</strong>.', 'profilebuilder' ); ?></p>
				<?php endif;?>
			</div>
			<div>
				<h3><?php _e( 'Email Customizer', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'Personalize all emails sent to your users or admins. On registration, email confirmation, admin approval / un-approval.', 'profilebuilder' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Custom Redirects', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'Keep your users out of the WordPress dashboard, redirect them to the front-page after login or registration, everything is just a few clicks away.', 'profilebuilder' ); ?></p>
			</div>
		</div>
		<div class="wppb-row wppb-3-col">
			<div>
				<h3><?php _e( 'Multiple Registration Forms', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'Set up multiple registration forms with different fields for certain user roles. Capture different information from different types of users.', 'profilebuilder' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Multiple Edit-profile Forms', 'profilebuilder' ); ?></h3>
				<p><?php _e( 'Allow different user roles to edit their specific information. Set up multiple edit-profile forms with different fields for certain user roles.', 'profilebuilder' ); ?></p>
			</div>
		</div>

		<?php
		//Output here Extra Features html for Hobbyist or Pro versions
		if ( $version != 'Free' ) echo $extra_features_html; ?>

		<hr/>
		<div>
			<h3>Extra Notes</h3>
			<ul>
				<li><?php printf( __( ' * only available in the %1$sHobbyist and Pro versions%2$s.', 'profilebuilder' ) ,'<a href="http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wpbackend&utm_medium=clientsite&utm_content=basicinfo-extranotes&utm_campaign=PB'.$version.'" target="_blank">', '</a>' );?></li>
				<li><?php printf( __( '** only available in the %1$sPro version%2$s.', 'profilebuilder' ), '<a href="http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wpbackend&utm_medium=clientsite&utm_content=basicinfo-extranotes&utm_campaign=PB'.$version.'" target="_blank">', '</a>' );?></li>
			</ul>
		</div>
	</div>
<?php
}