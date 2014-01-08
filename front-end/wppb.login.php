<?php
/* wp_signon can only be executed before anything is outputed in the page because of that we're adding it to the init hook */
global $wppb_login; 
$wppb_login = false;

function wppb_signon(){	
	global $error;
	global $wppb_login;
	global $wpdb;
	
	$wppb_generalSettings = get_option('wppb_general_settings');

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' && wp_verify_nonce($_POST['login_nonce_field'],'verify_true_login') && ($_POST['formName'] == 'login') ){
		$remember = ( ( isset( $_POST['remember-me'] ) && trim( $_POST['remember-me'] != '' ) ) ? trim( $_POST['remember-me'] ) : false );
		
		// if this setting is active, the posted username is, in fact the user's email
		if ( isset( $wppb_generalSettings['loginWith'] ) && ( $wppb_generalSettings['loginWith'] == 'email' ) ){
			$username = $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM $wpdb->users WHERE user_email= %s LIMIT 1", trim( $_POST['user-name'] ) ) );

			if ( $username == NULL )
				$username = trim( $_POST['user-name'] );

		}else
			$username = trim( $_POST['user-name'] );
	
		$wppb_login = wp_signon( array( 'user_login' => $username, 'user_password' => trim( $_POST['password'] ), 'remember' => $remember ), false );
		
	}elseif ( isset( $_GET['userName'] ) && isset( $_GET['passWord'] ) ){
		$password = base64_decode( trim( $_GET['passWord'] ) );
		
		// if this setting is active, the posted username is, in fact the user's email
		if ( isset( $wppb_generalSettings['loginWith'] ) && ( $wppb_generalSettings['loginWith'] == 'email' ) )
			$username = $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM $wpdb->users WHERE user_email= %s LIMIT 1", $username ) );
		
		$wppb_login = wp_signon( array( 'user_login' => $username, 'user_password' => base64_decode( trim( $_GET['passWord'] ) ), 'remember' => true ), false );
	}
}
add_action('init', 'wppb_signon');

function wppb_front_end_login( $atts ){
	$loginFilterArray = array();
	ob_start();
	global $wppb_login;
	
	$wppb_generalSettings = get_option('wppb_general_settings');

	extract(shortcode_atts(array('display' => true, 'redirect' => '', 'submit' => 'page'), $atts));
	
	echo '<div class="wppb_holder" id="wppb_login">';
	
	if ( is_user_logged_in() ){  // Already logged in 
		global $user_ID; 
		$wppb_user = get_userdata( $user_ID );
		
		if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email'))
			$display_name = $wppb_user->user_email;
		elseif($wppb_user->display_name !== '')
			$display_name = $wppb_user->user_login;
		else
			$display_name = $wppb_user->display_name;
		
		$loginFilterArray['loginMessage1'] = '<p class="alert">'. sprintf(__('You are currently logged in as %1$s. %2$s', 'profilebuilder'), '<a href="'.$authorPostsUrl = get_author_posts_url( $wppb_user->ID ).'" title="'.$display_name.'">'.$display_name.'</a>', '<a href="'.wp_logout_url( $redirectTo = wppb_curpageurl() ).'" title="'. __('Log out of this account', 'profilebuilder').'">'. __('Log out', 'profilebuilder').' &raquo;</a>') . '</p><!-- .alert-->';
		echo $loginFilterArray['loginMessage1'] = apply_filters('wppb_login_login_message1', $loginFilterArray['loginMessage1'], $wppb_user->ID, $display_name);	
	
	}elseif ( isset($wppb_login->ID) ){ // Successful login
		if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email'))	
			$display_name = $wppb_login->user_email;
		elseif($wppb_login->display_name !== '')
			$display_name = $wppb_login->user_login;
		else
			$display_name = $wppb_login->display_name;

		$loginFilterArray['loginMessage2'] = '<p class="success">'. sprintf(__('You have successfully logged in as %1$s', 'profilebuilder'), '<a href="'.$authorPostsUrl = get_author_posts_url( $wppb_login->ID ).'" title="'.$display_name.'">'.$display_name.'</a>') . '</p><!-- .success-->';
		echo $loginFilterArray['loginMessage2'] = apply_filters('wppb_login_login_message2', $loginFilterArray['loginMessage2'], $wppb_login->ID, $display_name);
		
		if (isset($_POST['button']) && isset($_POST['formName']) ){
			if ($_POST['formName'] == 'login'){
				if ($_POST['button'] == 'page'){
					$permaLnk2 = wppb_curpageurl();
				
					$wppb_addon_settings = get_option('wppb_addon_settings'); //fetch the descriptions array
					if ($wppb_addon_settings['wppb_customRedirect'] == 'show'){
						//check to see if the redirect location is not an empty string and is activated
						$customRedirectSettings = get_option('customRedirectSettings');
						if ((trim($customRedirectSettings['afterLoginTarget']) != '') && ($customRedirectSettings['afterLogin'] == 'yes')){
							$permaLnk2 = trim($customRedirectSettings['afterLoginTarget']);
							if (wppb_check_missing_http($permaLnk2))
								$permaLnk2 = 'http://'. $permaLnk2;
						}
					}
					
					$loginFilterArray['redirectMessage'] = '<font id="messageTextColor">' . sprintf(__('You will soon be redirected automatically. If you see this page for more than 1 second, please click %1$s', 'profilebuilder'), '<a href="'.$permaLnk2.'">'. __('here', 'profilebuilder').'</a>.<meta http-equiv="Refresh" content="1;url='.$permaLnk2.'" />') . '</font><br/><br/>';
					echo $loginFilterArray['redirectMessage'] = apply_filters('wppb_login_redirect_message', $loginFilterArray['redirectMessage'], $permaLnk2);

				}elseif($_POST['button'] == 'widget'){
					$permaLnk2 = wppb_curpageurl();
					if ($redirect != '')
						$permaLnk2 = trim($redirect);

						
					$loginFilterArray['widgetRedirectMessage'] = '<font id="messageTextColor">' . sprintf(__('You will soon be redirected automatically. If you see this page for more than 1 second, please click %1$s', 'profilebuilder'), '<a href="'.$permaLnk2.'">'. __('here', 'profilebuilder').'</a>.<meta http-equiv="Refresh" content="1;url='.$permaLnk2.'" />') . '</font><br/><br/>';
					echo $loginFilterArray['widgetRedirectMessage'] = apply_filters('wppb_login_widget_redirect_message', $loginFilterArray['widgetRedirectMessage'], $permaLnk2);
					
				}
			}
		}
					
	}else{ // Not logged in

		if (!empty( $_POST['action'] ) && isset($_POST['formName']) ){
			if ($_POST['formName'] == 'login'){
		?>
				<p class="error">
					<?php 					
					if ( ( isset( $_POST['user-name'] ) && isset( $_POST['password'] ) ) && ( ( trim( $_POST['user-name'] ) == '' ) && ( trim( $_POST['password']  == '' ) ) ) )
						echo $loginFilterArray['emptyFieldsError'] = apply_filters ( 'wppb_login_empty_fields_error_message', '<strong>'. __( 'ERROR:','profilebuilder' ).'</strong> '. __( 'Both fields are empty.', 'profilebuilder' ) );
						
					elseif ( is_wp_error( $wppb_login ) ){
						if ( ( isset( $wppb_generalSettings['loginWith'] ) && ( $wppb_generalSettings['loginWith'] == 'email' ) ) && isset( $wppb_login->errors['empty_username'] ) ){
							echo $loginFilterArray['emptyEmailError'] = apply_filters ( 'wppb_login_empty_email_error_message', '<strong>'. __( 'ERROR:','profilebuilder' ).'</strong> '. __( 'The email field is empty.', 'profilebuilder' ) );
						
						}else{
							$loginFilterArray['wpError'] = $wppb_login->get_error_message();
							echo $loginFilterArray['wpError'] = apply_filters( 'wppb_login_wp_error_message', $loginFilterArray['wpError'], $wppb_login );
						}
					}
					?>
				</p><!-- .error -->
		<?php
			}
		} 
		
		/* use this action hook to add extra content before the login form. */
		do_action( 'wppb_before_login' );?>
		
		<form action="<?php wppb_curpageurl(); ?>" method="post" class="sign-in" name="loginForm">
		<?php
			if ( isset( $_POST['user-name'] ) )
				$userName = esc_html( $_POST['user-name'] );
			else 
				$userName = '';
			
			if ( isset( $wppb_generalSettings['loginWith'] ) && ( $wppb_generalSettings['loginWith'] == 'email' ) )
				$loginWith = __( 'Email', 'profilebuilder' );
			else
				$loginWith = __( 'Username', 'profilebuilder' );
			
			$loginFilterArray['loginUsername'] = '
				<p class="login-form-username">
					<label for="user-name">'. $loginWith .'</label>
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
				<?php $button_name = __('Log in', 'profilebuilder'); ?>
				<input type="submit" name="submit" class="submit button" value="<?php echo apply_filters('wppb_login_button_name1', $button_name); ?>" />
				<?php
					$loginFilterArray['rememberMe'] = '
						<input class="remember-me checkbox" name="remember-me" id="remember-me" type="checkbox" checked="checked" value="forever" />
						<label for="remember-me">'. __('Remember me', 'profilebuilder').'</label>';
					$loginFilterArray['rememberMe'] = apply_filters('wppb_login_remember_me', $loginFilterArray['rememberMe']);
					echo $loginFilterArray['rememberMe'];
				?>

				<input type="hidden" name="action" value="log-in" />
				<input type="hidden" name="button" value="<?php echo apply_filters('wppb_login_button_name2', $submit); ?>" />
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
			wp_nonce_field('verify_true_login','login_nonce_field'); ?>
		</form><!-- .sign-in -->

	<?php 
	}
	
	/* use this action hook to add extra content after the login form. */
	do_action( 'wppb_after_login' );?>
	
	</div>
	<?php
	$output = ob_get_contents();
    ob_end_clean();
		
	$loginFilterArray = apply_filters('wppb_login', $loginFilterArray);

    return $output;
}