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

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' && wp_verify_nonce($_POST['login_nonce_field'],'verify_true_login') && ($_POST['formName'] == 'login') ){
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

function wppb_front_end_login( $atts ){
	$loginFilterArray = array();
	ob_start();
	global $wppb_login;

	extract(shortcode_atts(array('display' => true, 'redirect' => '', 'submit' => 'page'), $atts));
	
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
		
			$loginFilterArray['loginMessage1'] = apply_filters('wppb_login_login_message1', $loginFilterArray['loginMessage1'], $wppb_user->ID, $wppb_user->display_name);
			echo $loginFilterArray['loginMessage1'];		
		?>
	
	<?php elseif ( isset($wppb_login->ID) ) : // Successful login ?>
		<?php
			if($wppb_login->display_name == ''){ 
				$wppb_login->display_name = $wppb_login->user_login;
			}
			
		?>
			
			<?php	
				$loginFilterArray['loginMessage2'] = '
					<p class="success">'.
						 __('You have successfully logged in as', 'profilebuilder').' <a href="'.$authorPostsUrl = get_author_posts_url( $wppb_login->ID ).'" title="'.$wppb_login->display_name.'">'.$wppb_login->display_name.'</a>.
					</p><!-- .success-->';
			
				$loginFilterArray['loginMessage2'] = apply_filters('wppb_login_login_message2', $loginFilterArray['loginMessage2'], $wppb_login->ID, $wppb_login->display_name);
				echo $loginFilterArray['loginMessage2'];
			?>
			
			
				<?php
					if (isset($_POST['button']) && isset($_POST['formName']) ){
						if ($_POST['formName'] == 'login'){
							if ($_POST['button'] == 'page'){
								$permaLnk2 = get_permalink();
								$wppb_addons = WPPB_PLUGIN_DIR . '/premium/addon/';
								if (file_exists ( $wppb_addons.'addon.php' )){
									//check to see if the redirecting addon is present and activated
									$wppb_addon_settings = get_option('wppb_addon_settings'); //fetch the descriptions array
									if ($wppb_addon_settings['wppb_customRedirect'] == 'show'){
										//check to see if the redirect location is not an empty string and is activated
										$customRedirectSettings = get_option('customRedirectSettings');
										if ((trim($customRedirectSettings['afterLoginTarget']) != '') && ($customRedirectSettings['afterLogin'] == 'yes')){
											$permaLnk2 = trim($customRedirectSettings['afterLoginTarget']);
											$findHttp = strpos( (string)$permaLnk2, 'http' );
											if ($findHttp === false)
												$permaLnk2 = 'http://'. $permaLnk2;
										}
									}
								}
								
								$loginFilterArray['redirectMessage'] = '
									<font id="messageTextColor">'. __('You will soon be redirected automatically. If you see this page for more than 1 second, please click', 'profilebuilder').' <a href="'.$permaLnk2.'">'. __('here', 'profilebuilder').'</a>.<meta http-equiv="Refresh" content="1;url='.$permaLnk2.'" /></font><br/><br/>';
								$loginFilterArray['redirectMessage'] = apply_filters('wppb_login_redirect_message', $loginFilterArray['redirectMessage'], $permaLnk2);
								echo $loginFilterArray['redirectMessage'];

							}elseif($_POST['button'] == 'widget'){
								$permaLnk2 = get_permalink();
								if ($redirect != ''){
									$permaLnk2 = trim($redirect);
									$findHttp = strpos( (string)$permaLnk2, 'http' );
									if ($findHttp === false)
										$permaLnk2 = 'http://'. $permaLnk2;
								}
									
								$loginFilterArray['widgetRedirectMessage'] = '
									<font id="messageTextColor">'. __('You will soon be redirected automatically. If you see this page for more than 1 second, please click', 'profilebuilder').' <a href="'.$permaLnk2.'">'. __('here', 'profilebuilder').'</a>.<meta http-equiv="Refresh" content="1;url='.$permaLnk2.'" /></font><br/><br/>';
								$loginFilterArray['redirectMessage'] = apply_filters('wppb_login_widget_redirect_message', $loginFilterArray['widgetRedirectMessage'], $permaLnk2);
								echo $loginFilterArray['widgetRedirectMessage'];
								
							}
						}
					}
					
				?>
	<?php else : // Not logged in ?>

			<?php 
			if (!empty( $_POST['action'] ) && isset($_POST['formName']) ){
				if ($_POST['formName'] == 'login'){
			?>
					<p class="error">
						<?php 
						if ( trim($_POST['user-name']) == ''){
							$loginFilterArray['emptyUsernameError'] = '<strong>'. __('ERROR:','profilebuilder').'</strong> '. __('The username field is empty', 'profilebuilder').'.'; 
							$loginFilterArray['emptyUsernameError'] = apply_filters('wppb_login_empty_username_error_message', $loginFilterArray['emptyUsernameError']);
							echo $loginFilterArray['emptyUsernameError'];
						}	
						if ( is_wp_error($wppb_login) ){
							$loginFilterArray['wpError'] = $wppb_login->get_error_message();
							$loginFilterArray['wpError'] = apply_filters('wppb_login_wp_error_message', $loginFilterArray['wpError'],$wppb_login);
							echo $loginFilterArray['wpError'];
						}
						?>
					</p><!-- .error -->
			<?php
				}
			} 
			?>
		
		<?php /* use this action hook to add extra content before the login form. */ ?>
		<?php do_action( 'wppb_before_login' ); ?> 
		
		<form action="<?php wppb_curpageurl(); ?>" method="post" class="sign-in" name="loginForm">
		<?php
			if (isset($_POST['user-name']))
				$userName = esc_html( $_POST['user-name'] );
			else $userName = '';
			
			$loginFilterArray['loginUsername'] = '
				<p class="login-form-username">
					<label for="user-name">'. __('Username', 'profilebuilder') .'</label>
					<input type="text" name="user-name" id="user-name" class="text-input" value="'.$userName.'" />
				</p><!-- .form-username -->';
			$loginFilterArray['loginUsername'] = apply_filters('wppb_login_username', $loginFilterArray['loginUsername'], $userName);
			echo $loginFilterArray['loginUsername'];

			$loginFilterArray['loginPassword'] = '
				<p class="login-form-password">
					<label for="password">'. __('Password', 'profilebuilder') .'</label>
					<input type="password" name="password" id="password" class="text-input" />
				</p><!-- .form-password -->';
			$loginFilterArray['loginPassword'] = apply_filters('wppb_login_password', $loginFilterArray['loginPassword']);
			echo $loginFilterArray['loginPassword'];
				
		?>
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
				<input type="hidden" name="button" value="<?php echo $submit;?>" />
				<input type="hidden" name="formName" value="login" />
			</p><!-- .form-submit -->
			<?php
				if ($display === true){
					$siteURL=get_option('siteurl').'/wp-login.php?action=lostpassword';
					$siteURL = apply_filters('wppb_pre_login_url_filter', $siteURL);
					$loginFilterArray['loginURL'] = '
						<p>
							<a href="'.$siteURL.'">'. __('Lost password?', 'profilebuilder').'</a>
						</p>';
					$loginFilterArray['loginURL'] = apply_filters('wppb_login_url', $loginFilterArray['loginURL'], $siteURL);
					echo $loginFilterArray['loginURL'];
				}
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