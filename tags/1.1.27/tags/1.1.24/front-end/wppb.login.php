<?php
if(!function_exists('wppb_curpageurl')){
    function wppb_curpageurl() {
     $pageURL = 'http';
     if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")) {
		$pageURL .= "s";
	 }
     $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }
     return $pageURL;
    }
}

/* wp_signon can only be executed before anything is outputed in the page because of that we're adding it to the init hook */
global $wppb_login; 
$wppb_login = false;

function wppb_signon(){	
	global $error;
	global $wppb_login;

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' && wp_verify_nonce($_POST['login_nonce_field'],'verify_true_login')){
		if (isset($_POST['remember-me']))
			$remember = $_POST['remember-me'];
		else $remember = false;
		$wppb_login = wp_signon( array( 'user_login' => $_POST['user-name'], 'user_password' => $_POST['password'], 'remember' => $_POST['remember-me'] ), false );
		
	}elseif (isset($_GET['userName']) && isset($_GET['passWord'])){
		$remember = true;
		$username = $_GET['userName'];
		$password = base64_decode($_GET['passWord']);
		$wppb_login = wp_signon( array( 'user_login' => $username, 'user_password' => $password, 'remember' => $remember ), false );
	}
}
add_action('init', 'wppb_signon');

function wppb_front_end_login(){
	$loginFilterArray = array();
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
		<?php
			$loginFilterArray['loginMessage1'] = '
				<p class="alert">'.
					__('You are currently logged in as', 'profilebuilder').' <a href="'.$authorPostsUrl = get_author_posts_url( $wppb_user->ID ).'" title="'.$wppb_user->display_name.'">'.$wppb_user->display_name.'</a>.
					<a href="'.wp_logout_url( get_permalink() ).'" title="'. __('Log out of this account', 'profilebuilder').'">'. __('Log out', 'profilebuilder').' &raquo;</a>
				</p><!-- .alert-->';
		
			$loginFilterArray['loginMessage1'] = apply_filters('wppb_login_login_message1', $loginFilterArray['loginMessage1']);
			echo $loginFilterArray['loginMessage1'];
		?>
	
	<?php elseif ( $wppb_login->ID ) : // Successful login ?>
		<?php
			//$wppb_login = get_userdata( $wppb_login->ID ); 
			if($wppb_login->display_name == ''){ 
				$wppb_login->display_name = $wppb_login->user_login;
			}
			
		?>
			
			<?php	
				$loginFilterArray['loginMessage2'] = '
					<p class="success">'.
						 __('You have successfully logged in as', 'profilebuilder').' <a href="'.$authorPostsUrl = get_author_posts_url( $wppb_login->ID ).'" title="'.$wppb_login->display_name.'">'.$wppb_login->display_name.'</a>.
					</p><!-- .success-->';
			
				$loginFilterArray['loginMessage2'] = apply_filters('wppb_login_login_message2', $loginFilterArray['loginMessage2']);
				echo $loginFilterArray['loginMessage2'];
			?>
			
			
				<?php 
					$permaLnk2 = get_permalink();
					$wppb_addons = wppb_plugin_dir . '/premium/addon/';
					if (file_exists ( $wppb_addons.'addon.php' )){
						//check to see if the redirecting addon is present and activated
						$wppb_premium_addon_settings = get_option('wppb_premium_addon_settings'); //fetch the descriptions array
						if ($wppb_premium_addon_settings['customRedirect'] == 'show'){
							//check to see if the redirect location is not an empty string and is activated
							$customRedirectSettings = get_option('customRedirectSettings');
							if ((trim($customRedirectSettings['afterLoginTarget']) != '') && ($customRedirectSettings['afterLogin'] == 'yes')){
								$permaLnk2 = trim($customRedirectSettings['afterLoginTarget']);
								$findHttp = strpos($permaLnk2, 'http');
								if ($findHttp === false)
									$permaLnk2 = 'http://'. $permaLnk2;
							}
						}
					}

					$loginFilterArray['redirectMessage'] = '
						<font color="black">'. __('You will soon be redirected automatically. If you see this page for more than 1 second, please click', 'profilebuilder').' <a href="'.$permaLnk2.'">'. __('here', 'profilebuilder').'</a>.<meta http-equiv="Refresh" content="1;url='.$permaLnk2.'" /></font><br/><br/>';
					$loginFilterArray['redirectMessage'] = apply_filters('wppb_login_redirect_message', $loginFilterArray['redirectMessage']);
					echo $loginFilterArray['redirectMessage'];
				?>
	<?php else : // Not logged in ?>

		<?php if (!empty( $_POST['action'] )): ?>
			<p class="error">
				<?php if ( trim($_POST['user-name']) == '') echo '<strong>'. __('ERROR:','profilebuilder').'</strong> '. __('The username field is empty', 'profilebuilder').'. '; ?>
				<?php if ( is_wp_error($wppb_login) ) echo $wppb_login->get_error_message();?>
			</p><!-- .error -->
		<?php endif; ?>
		
		<?php /* use this action hook to add extra content before the login form. */ ?>
		<?php do_action( 'wppb_before_login' ); ?> 
		
		<form action="<?php wppb_curpageurl(); ?>" method="post" class="sign-in">
			<p class="login-form-username">
				<label for="user-name"><?php _e('Username', 'profilebuilder'); ?></label>
				<?php
					if (isset($_POST['user-name']))
						$userName = esc_html( $_POST['user-name'] );
					else $userName = '';
				?>
				<?php echo '<input type="text" name="user-name" id="user-name" class="text-input" value="'.$userName.'" />'; ?>
			</p><!-- .form-username -->

			<p class="login-form-password">
				<label for="password"><?php _e('Password', 'profilebuilder'); ?></label>
				<input type="password" name="password" id="password" class="text-input" />
			</p><!-- .form-password -->
			<p class="login-form-submit">
				<input type="submit" name="submit" class="submit button" value="<?php _e('Log in', 'profilebuilder'); ?>" />
				<?php
					$loginFilterArray['rememberMe'] = '
						<input class="remember-me checkbox" name="remember-me" id="remember-me" type="checkbox" checked="checked" value="forever" />
						<label for="remember-me">'. __('Remember me', 'profilebuilder').'</label>';
					$loginFilterArray['rememberMe'] = apply_filters('wppb_login_remember_me', $loginFilterArray['rememberMe']);
					echo $loginFilterArray['rememberMe'];
				?>

				<input type="hidden" name="action" value="log-in" />
			</p><!-- .form-submit -->
			<?php
				$loginFilterArray['loginURL'] = '
					<p>
						<a href="'.$siteURL=get_option('siteurl').'/wp-login.php?action=lostpassword">'. __('Lost password?', 'profilebuilder').'</a>
					</p>';
				$loginFilterArray['loginURL'] = apply_filters('wppb_login_url', $loginFilterArray['loginURL']);
				echo $loginFilterArray['loginURL'];
			?>
			<?php wp_nonce_field('verify_true_login','login_nonce_field'); ?>
		</form><!-- .sign-in -->

	<?php endif;?>
	
	<?php /* use this action hook to add extra content after the login form. */ ?>
	<?php do_action( 'wppb_after_login' ); ?> 
	
	</div>
	<?php
	$output = ob_get_contents();
    ob_end_clean();
		
	$loginFilterArray = apply_filters('wppb_login', $loginFilterArray);

    return $output;
}