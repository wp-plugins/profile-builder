<?php




// wp_signon can only be executed before anything is outputed in the page
// because of that we're adding it to the init hook
$wppb_login = false;

function wppb_signon(){
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' && wp_verify_nonce($_POST['login_nonce_field'],'verify_true_login')) :
		global $error;
		global $wppb_login; 
		$wppb_login = wp_signon( array( 'user_login' => $_POST['user-name'], 'user_password' => $_POST['password'], 'remember' => $_POST['remember-me'] ), false );
	endif;
}
add_action('init', 'wppb_signon');

function wppb_front_end_login(){
	ob_start();
	global $wppb_login;
	
	echo '<div class="wppb_holder" id="wppb_login">';
	
	if ( is_user_logged_in() ) : // Already logged in 
		global $user_ID; 
		$wppb_user = get_userdata( $user_ID );
		if($wppb_user->display_name == ''){ 
			$wppb_user->display_name = $wppb_user->user_login;
		}
		
	?>
	
	
	
		
		<p class="alert">
			<?php printf( __('You are currently logged in as <a href="%1$s" title="%2$s">%2$s</a>.', 'profilebuilder'), get_author_posts_url( $wppb_user->ID ), $wppb_user->display_name ); ?> <a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="<?php _e('Log out of this account', 'profilebuilder'); ?>"><?php _e('Log out &raquo;', 'profilebuilder'); ?></a>
		</p><!-- .alert -->
	
	<?php elseif ( $wppb_login->ID ) : // Successful login ?>
		<?php
			//$wppb_login = get_userdata( $wppb_login->ID ); 
			if($wppb_login->display_name == ''){ 
				$wppb_login->display_name = $wppb_login->user_login;
			}
			
		?>

		<p class="success">
				<?php printf( __('You have successfully logged in as <a href="%1$s" title="%2$s">%2$s</a>.', 'profilebuilder'), get_author_posts_url( $wppb_login->ID ), $wppb_login->display_name ); ?>
		</p><!-- .success-->
	<?php else : // Not logged in ?>

		<?php if (!empty( $_POST['action'] )): ?>
			<p class="error">
				<?php if ( trim($_POST['user-name']) == '') echo '<strong>ERROR:</strong> The username field is empty. '; ?>
				<?php if ( is_wp_error($wppb_login) ) echo $wppb_login->get_error_message();?>
			</p><!-- .error -->
		<?php endif; ?>
		
		<form action="<?php the_permalink(); ?>" method="post" class="sign-in">
			<p class="login-form-username">
				<label for="user-name"><?php _e('Username', 'profilebuilder'); ?></label>
				<input type="text" name="user-name" id="user-name" class="text-input" value="<?php echo wp_specialchars( $_POST['user-name'], 1 ); ?>" />
			</p><!-- .form-username -->

			<p class="login-form-password">
				<label for="password"><?php _e('Password', 'profilebuilder'); ?></label>
				<input type="password" name="password" id="password" class="text-input" />
			</p><!-- .form-password -->
			<p class="login-form-submit">
				<input type="submit" name="submit" class="submit button" value="<?php _e('Log in', 'profilebuilder'); ?>" />
				<input class="remember-me checkbox" name="remember-me" id="remember-me" type="checkbox" checked="checked" value="forever" />
				<label for="remember-me"><?php _e('Remember me', 'profilebuilder'); ?></label>
				<input type="hidden" name="action" value="log-in" />
			</p><!-- .form-submit -->
			<p>
				<a href="<?php echo get_option('siteurl');  ?>/wp-login.php?action=lostpassword"><?php _e('Lost password?', 'profilebuilder'); ?></a>
			</p>
			<?php wp_nonce_field('verify_true_login','login_nonce_field'); ?>
		</form><!-- .sign-in -->

	<?php endif;?>
	</div>
	<?php
	
	$output = ob_get_contents();
    ob_end_clean();
    return $output;
	
}