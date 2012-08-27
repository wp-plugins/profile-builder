<?php
//function needed to check if the current page already has a ? sign in the address bar
if(!function_exists('wppb_curpageurl_password_recovery')){
    function wppb_curpageurl_password_recovery() {
		$pageURL = 'http';
		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")) {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	
		$questionPos = strpos($pageURL, '?');
		$submitedPos = strpos($pageURL, 'submited=yes');
		
		if ($submitedPos !== false)
			return $pageURL;
		elseif($questionPos !== false)
			return $pageURL.'&submited=yes';
		else
			return $pageURL.'?submited=yes';
    }
}

if(!function_exists('wppb_curpageurl_password_recovery2')){
    function wppb_curpageurl_password_recovery2($user_login, $id) {
	
		global $wpdb;
		$pageURL = 'http';
		
		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")) {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	
		$questionPos = strpos($pageURL, '?');
		$key = md5($user_login.'RMPBP'.$id.'PWRCVR');
		
		if($questionPos !== false){
			$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
			return $pageURL.'&loginName='.$user_login.'&key='.$key;
		}else{
			$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
			return $pageURL.'?loginName='.$user_login.'&key='.$key;
		}
    }
}

if(!function_exists('wppb_curpageurl_password_recovery3')){
    function wppb_curpageurl_password_recovery3() {
		$pageURL = 'http';
		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")) {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	
		$questionPos = strpos($pageURL, '?');
		$finalActionPos = strpos($pageURL, 'finalAction=yes');
		
		if ($finalActionPos !== false)
			return $pageURL;
		elseif($questionPos !== false)
			return $pageURL.'&finalAction=yes';
		else
			return $pageURL.'?finalAction=yes';
    }
}



function wppb_front_end_password_recovery(){
	$recoverPasswordFilterArray = array();
	$message = '';
	$messageNo = '';	
	$message2 = '';
	$messageNo2 = '';
	
	global $wpdb;
	//global $current_user;
    //get_currentuserinfo();
	
	$linkLoginName = '';
	$linkKey = '';
	
	ob_start();

	
	/* If the user entered an email/username, process the request */
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'recover_password' && wp_verify_nonce($_POST['password_recovery_nonce_field'],'verify_true_password_recovery') ) {
		
		$postedData = $_POST['username_email'];	//we get the raw data
		//check to see if it's an e-mail (and if this is valid/present in the database) or is a username
		if (is_email($postedData)){
			if (email_exists($postedData)){
				$recoverPasswordFilterArray['sentMessage1'] = __('A password reset email has been sent to ', 'profilebuilder').$postedData.'. <br/>'.__('Following the link sent in the email address will reset the password.', 'profilebuilder');
				$recoverPasswordFilterArray['sentMessage1'] = apply_filters('wppb_recover_password_sent_message1', $recoverPasswordFilterArray['sentMessage1']);
				$messageNo = '1';
				$message = $recoverPasswordFilterArray['sentMessage1'];
				
				//verify e-mail validity
				$query = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE user_email='".$postedData."'");
				$requestedUserID = $query[0]->ID;
				$requestedUserLogin = $query[0]->user_login; 
				$requestedUserEmail = $query[0]->user_email; 
				
				//send primary email message
				$recoverPasswordFilterArray['userMailMessage1']  = __('Someone requested that the password be reset for the following account: ', 'profilebuilder');
				$recoverPasswordFilterArray['userMailMessage1'] .= '<b>'.$requestedUserLogin.'</b><br/>';
				$recoverPasswordFilterArray['userMailMessage1'] .= __('If this was a mistake, just ignore this email and nothing will happen.', 'profilebuilder').'<br/>';
				$recoverPasswordFilterArray['userMailMessage1'] .= __('To reset your password, visit the following link:', 'profilebuilder');
				$recoverPasswordFilterArray['userMailMessage1'] .= '<a href="'.wppb_curpageurl_password_recovery2($requestedUserLogin, $requestedUserID).'">'.wppb_curpageurl_password_recovery2($requestedUserLogin, $requestedUserID).'</a>';
				$recoverPasswordFilterArray['userMailMessage1']  = apply_filters('wppb_recover_password_message_content_sent_to_user1', $recoverPasswordFilterArray['userMailMessage1']);
				
				$recoverPasswordFilterArray['userMailMessageTitle1'] = __('Password Reset Feature from', 'profilebuilder').' "'.$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES).'"';
				$recoverPasswordFilterArray['userMailMessageTitle1'] = apply_filters('wppb_recover_password_message_title_sent_to_user1', $recoverPasswordFilterArray['userMailMessageTitle1']);
				
				//we add this filter to enable html encoding
				add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
				//send mail to the user notifying him of the reset request
				if (trim($recoverPasswordFilterArray['userMailMessageTitle1']) != ''){
					$sent = wp_mail($requestedUserEmail, $recoverPasswordFilterArray['userMailMessageTitle1'], $recoverPasswordFilterArray['userMailMessage1']);
					if ($sent === false){
						$recoverPasswordFilterArray['sentMessageCouldntSendMessage'] = '<b>'. __('ERROR', 'profilebuilder') .': </b>'.__('There was an error while trying to send the activation link to ', 'profilebuilder').$postedData.'!';
						$recoverPasswordFilterArray['sentMessageCouldntSendMessage'] = apply_filters('wppb_recover_password_sent_message_error_sending', $recoverPasswordFilterArray['sentMessageCouldntSendMessage']);
						$messageNo = '5';
						$message = $recoverPasswordFilterArray['sentMessageCouldntSendMessage'];
					}
				}
				
				
				
			}elseif (!email_exists($postedData)){
				$recoverPasswordFilterArray['sentMessage2'] = __('The email address entered wasn\'t found in the database!', 'profilebuilder').'<br/>'.__('Please check that you entered the correct email address.', 'profilebuilder');
				$recoverPasswordFilterArray['sentMessage2'] = apply_filters('wppb_recover_password_sent_message2', $recoverPasswordFilterArray['sentMessage2']);
				$messageNo = '2';
				$message = $recoverPasswordFilterArray['sentMessage2'];
			}
		}elseif (!is_email($postedData)){
			if (username_exists($postedData)){	
				$recoverPasswordFilterArray['sentMessage3'] = __('A password reset email has been sent to ', 'profilebuilder').$postedData.'. <br/>'.__('Following the link sent in the email address will reset the password.', 'profilebuilder');
				$recoverPasswordFilterArray['sentMessage3'] = apply_filters('wppb_recover_password_sent_message3', $recoverPasswordFilterArray['sentMessage3']);
				$messageNo = '3';
				$message = $recoverPasswordFilterArray['sentMessage3'];
				
				//verify username validity
				$query = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE user_login='".$postedData."'");
				$requestedUserID = $query[0]->ID;
				$requestedUserLogin = $query[0]->user_login; 
				$requestedUserEmail = $query[0]->user_email; 

				//send primary email message
				$recoverPasswordFilterArray['userMailMessage1']  = __('Someone requested that the password be reset for the following account: ', 'profilebuilder');
				$recoverPasswordFilterArray['userMailMessage1'] .= '<b>'.$requestedUserLogin.'</b><br/>';
				$recoverPasswordFilterArray['userMailMessage1'] .= __('If this was a mistake, just ignore this email and nothing will happen.', 'profilebuilder').'<br/>';
				$recoverPasswordFilterArray['userMailMessage1'] .= __('To reset your password, visit the following link:', 'profilebuilder');
				$recoverPasswordFilterArray['userMailMessage1'] .= '<a href="'.wppb_curpageurl_password_recovery2($requestedUserLogin, $requestedUserID).'">'.wppb_curpageurl_password_recovery2($requestedUserLogin, $requestedUserID).'</a>';
				$recoverPasswordFilterArray['userMailMessage1']  = apply_filters('wppb_recover_password_message_content_sent_to_user1', $recoverPasswordFilterArray['userMailMessage1']);
				
				$recoverPasswordFilterArray['userMailMessageTitle1'] = __('Password Reset Feature from', 'profilebuilder').' "'.$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES).'"';
				$recoverPasswordFilterArray['userMailMessageTitle1'] = apply_filters('wppb_recover_password_message_title_sent_to_user1', $recoverPasswordFilterArray['userMailMessageTitle1']);
				
				//we add this filter to enable html encoding
				add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
				//send mail to the user notifying him of the reset request
				if (trim($recoverPasswordFilterArray['userMailMessageTitle1']) != ''){
					$sent = wp_mail($requestedUserEmail, $recoverPasswordFilterArray['userMailMessageTitle1'], $recoverPasswordFilterArray['userMailMessage1']);
					if ($sent === false){
							$recoverPasswordFilterArray['sentMessageCouldntSendMessage'] = '<b>'. __('ERROR', 'profilebuilder') .': </b>'.__('There was an error while trying to send the activation link to ', 'profilebuilder').$postedData.'!';
							$recoverPasswordFilterArray['sentMessageCouldntSendMessage'] = apply_filters('wppb_recover_password_sent_message_error_sending', $recoverPasswordFilterArray['sentMessageCouldntSendMessage']);
							$messageNo = '5';
							$message = $recoverPasswordFilterArray['sentMessageCouldntSendMessage'];
						}				
				}
			}elseif (!username_exists($postedData)){
				$recoverPasswordFilterArray['sentMessage4'] = __('The username entered wasn\'t found in the database!', 'profilebuilder').'<br/>'.__('Please check that you entered the correct username.', 'profilebuilder');
				$recoverPasswordFilterArray['sentMessage4'] = apply_filters('wppb_recover_password_sent_message4', $recoverPasswordFilterArray['sentMessage4']);
				$messageNo = '4';
				$message = $recoverPasswordFilterArray['sentMessage4'];
			}
		}	
		
	}
	/* If the user used the correct key-code, update his/her password */
	elseif ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action2'] ) && $_POST['action2'] == 'recover_password2' && wp_verify_nonce($_POST['password_recovery_nonce_field2'],'verify_true_password_recovery2') ) {
		if (($_POST['passw1'] == $_POST['passw2']) && (!empty($_POST['passw1']) && !empty($_POST['passw2']))){
			$message2 = __('Your password has been successfully changed!', 'profilebuilder');
			$messageNo2 = '1';
			if ((isset($_GET['loginName'])) && (isset($_GET['finalAction']))){
				$loginName = $_GET['loginName'];
			}
			//update the new password and delete the key
			$query2 = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE user_login='".$loginName."'");
			wp_update_user( array( 'ID' => $query2[0]->ID, 'user_pass' => esc_attr( $_POST['passw1'] ) ) );
			$wpdb->update($wpdb->users, array('user_activation_key' => ''), array('user_login' => $loginName));
			
			//send secondary mail to the user containing the username and the new password
			$recoverPasswordFilterArray['userMailMessage2']  = __('You have successfully reset your password,', 'profilebuilder');
			$recoverPasswordFilterArray['userMailMessage2'] .= ' <b>'.$loginName.'</b>';
			$recoverPasswordFilterArray['userMailMessage2']  = apply_filters('wppb_recover_password_message_content_sent_to_user2', $recoverPasswordFilterArray['userMailMessage2']);
			
			$recoverPasswordFilterArray['userMailMessageTitle2'] = __('Password Successfully Reset for', 'profilebuilder') .' '.$query2[0]->user_login.' '. __('from', 'profilebuilder').' "'.$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES).'"';
			$recoverPasswordFilterArray['userMailMessageTitle2'] = apply_filters('wppb_recover_password_message_title_sent_to_user2', $recoverPasswordFilterArray['userMailMessageTitle2']);
			
			//we add this filter to enable html encoding
			add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
			//send mail to the user notifying him of the reset request
			if (trim($recoverPasswordFilterArray['userMailMessageTitle2']) != '')
				wp_mail($query2[0]->user_email, $recoverPasswordFilterArray['userMailMessageTitle2'], $recoverPasswordFilterArray['userMailMessage2']);
			
			//send email to admin
			$recoverPasswordFilterArray['adminMailMessage']  = $loginName. ' '.__('has requested a password change via the password reset feature.');
			$recoverPasswordFilterArray['adminMailMessage'] .= '<br/>'.__('His/her new password is:','profilebuilder'). ' '.$_POST['passw1'];
			$recoverPasswordFilterArray['adminMailMessage'] = apply_filters('wppb_recover_password_message_content_sent_to_admin', $recoverPasswordFilterArray['adminMailMessage']);

			$recoverPasswordFilterArray['adminMailMessageTitle'] = __('Password Successfully Reset for', 'profilebuilder') .' '.$query2[0]->user_login.' '. __('from', 'profilebuilder').' "'.$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES).'"';
			$recoverPasswordFilterArray['adminMailMessageTitle'] = apply_filters('wppb_recover_password_message_title_sent_to_admin', $recoverPasswordFilterArray['adminMailMessageTitle']);
			
			
			//we disable the feature to send the admin a notification mail but can be still used using filters
			$recoverPasswordFilterArray['adminMailMessageTitle'] = '';
			$recoverPasswordFilterArray['adminMailMessageTitle'] = apply_filters('wppb_recover_password_message_title_sent_to_admin', $recoverPasswordFilterArray['adminMailMessageTitle']);
			
			//we add this filter to enable html encoding
			add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
			//send mail to the admin notifying him of of a user with a password reset request
			if (trim($recoverPasswordFilterArray['adminMailMessageTitle']) != '') 
				wp_mail(get_option('admin_email'), $recoverPasswordFilterArray['adminMailMessageTitle'], $recoverPasswordFilterArray['adminMailMessage']);
			
		}else{
			$message2 = __('The entered passwords don\'t match!', 'profilebuilder');
			$messageNo2 = '2';
		}
			
	}
	
?>

	<div class="wppb_holder" id="wppb_recover_password">

<?php
			/* use this action hook to add extra content before the password recovery form. */
			do_action( 'wppb_before_recover_password_fields' );

			//this is the part that handles the actual recovery
			if (isset($_GET['submited']) && isset($_GET['loginName']) && isset($_GET['key'])){
				//get the login name and key and verify if they match the ones in the database
				$query = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE user_login='".$_GET['loginName']."'");
				$dbValue = $query[0]->user_activation_key;
				$id = $query[0]->ID;
				$localHashValue = md5($_GET['loginName'].'RMPBP'.$id.'PWRCVR');
				if ($localHashValue == $_GET['key']){
					//check if the "finalAction" variable is not in the address bar, if it is, don't display the form anymore
					if (isset($_GET['finalAction']) && ($_GET['finalAction'] == 'yes')){
						if ($messageNo2 == '2'){
							$recoverPasswordFilterArray['passwordChangedMessage2'] = '<p class="error">'. $message2 .'</p><!-- .error -->';
							$recoverPasswordFilterArray['passwordChangedMessage2'] = apply_filters ('wppb_recover_password_password_changed_message2', $recoverPasswordFilterArray['passwordChangedMessage2']);
							echo $recoverPasswordFilterArray['passwordChangedMessage2'];
?>
							<form enctype="multipart/form-data" method="post" id="recover_password2" class="user-forms" action="<?php echo $url=wppb_curpageurl_password_recovery3();?>">
<?php
								$recoverPasswordFilterArray['inputPassword'] = '
									<p class="passw1">
										<label for="passw1">'. __('Password', 'profilebuilder').'</label>
										<input class="password" name="passw1" type="password" id="passw1" value="'.$_POST['passw1'].'" />
									</p><!-- .passw1 -->
									
									<p class="passw2">
										<label for="passw2">'. __('Repeat Password', 'profilebuilder').'</label>
										<input class="password" name="passw2" type="password" id="passw2" value="'.$_POST['passw2'].'" />
									</p><!-- .passw2 -->';
								$recoverPasswordFilterArray['inputPassword'] = apply_filters('wppb_recover_password_input', $recoverPasswordFilterArray['inputPassword']);
								echo $recoverPasswordFilterArray['inputPassword'];
?>
								<p class="form-submit">
									<input name="recover_password2" type="submit" id="recover_password2" class="submit button" value="<?php _e('Reset Password', 'profilebuilder'); ?>" />
									<input name="action2" type="hidden" id="action2" value="recover_password2" />
								</p><!-- .form-submit -->
								<?php wp_nonce_field('verify_true_password_recovery2', 'password_recovery_nonce_field2'); ?>
							</form><!-- #recover_password -->
<?php
						}elseif ($messageNo2 == '1'){
							$recoverPasswordFilterArray['passwordChangedMessage1'] = '<p class="success">'. $message2 .'</p><!-- .success -->';
							$recoverPasswordFilterArray['passwordChangedMessage1'] = apply_filters ('wppb_recover_password_password_changed_message1', $recoverPasswordFilterArray['passwordChangedMessage1']);
							echo $recoverPasswordFilterArray['passwordChangedMessage1'];
						}
							
					}else{
					
?>
						<form enctype="multipart/form-data" method="post" id="recover_password2" class="user-forms" action="<?php echo $url=wppb_curpageurl_password_recovery3();?>">
<?php
							$recoverPasswordFilterArray['inputPassword'] = '
								<p class="passw1">
									<label for="passw1">'. __('Password', 'profilebuilder').'</label>
									<input class="password" name="passw1" type="password" id="passw1" value="'.$_POST['passw1'].'" />
								</p><!-- .passw1 -->
								
								<p class="passw2">
									<label for="passw2">'. __('Repeat Password', 'profilebuilder').'</label>
									<input class="password" name="passw2" type="password" id="passw2" value="'.$_POST['passw2'].'" />
								</p><!-- .passw2 -->';
							$recoverPasswordFilterArray['inputPassword'] = apply_filters('wppb_recover_password_input', $recoverPasswordFilterArray['inputPassword']);
							echo $recoverPasswordFilterArray['inputPassword'];
?>
							<p class="form-submit">
								<input name="recover_password2" type="submit" id="recover_password2" class="submit button" value="<?php _e('Reset Password', 'profilebuilder'); ?>" />
								<input name="action2" type="hidden" id="action2" value="recover_password2" />
							</p><!-- .form-submit -->
							<?php wp_nonce_field('verify_true_password_recovery2', 'password_recovery_nonce_field2'); ?>
						</form><!-- #recover_password -->
<?php
					}
				}else{
					$recoverPasswordFilterArray['invalidKeyMessage'] = '<p class="warning"><b>'. __('ERROR:', 'profilebuilder') .'</b> '. __('Invalid key!', 'profilebuilder') .'</p><!-- .warning -->';
					echo $recoverPasswordFilterArray['invalidKeyMessage'] = apply_filters('wppb_recover_password_invalid_key_message', $recoverPasswordFilterArray['invalidKeyMessage']);
				}
				
			}else{
				//display error message and the form
				if (($messageNo == '') || ($messageNo == '2') || ($messageNo == '4')){
					$recoverPasswordFilterArray['messageDisplay1'] = '
						<p class="warning">'.$message.'</p><!-- .warning -->';
					$recoverPasswordFilterArray['messageDisplay1'] = apply_filters('wppb_recover_password_displayed_message1', $recoverPasswordFilterArray['messageDisplay1']);
					echo $recoverPasswordFilterArray['messageDisplay1'];
					
					echo '<form enctype="multipart/form-data" method="post" id="recover_password" class="user-forms" action="'.$address = wppb_curpageurl_password_recovery().'">';
				
						$recoverPasswordFilterArray['notification'] = __('Please enter your username or email address.', 'profilebuilder').'<br/>'.__('You will receive a link to create a new password via email.', 'profilebuilder').'<br/><br/>';
						$recoverPasswordFilterArray['notification'] = apply_filters('wppb_recover_password_message1', $recoverPasswordFilterArray['notification']);
						echo $recoverPasswordFilterArray['notification'];
						
						$username_email = '';
						if (isset($_POST['username_email']))
							$username_email = $_POST['username_email'];
						$recoverPasswordFilterArray['input'] = '
							<p class="username_email">
								<label for="username_email">'. __('Username or E-mail', 'profilebuilder').'</label>
								<input class="text-input" name="username_email" type="text" id="username_email" value="'.trim($username_email).'" />
							</p><!-- .username_email -->';
						$recoverPasswordFilterArray['input'] = apply_filters('wppb_recover_password_input', $recoverPasswordFilterArray['input']);
						echo $recoverPasswordFilterArray['input'];
					
				
	?>	
						<p class="form-submit">
							<input name="recover_password" type="submit" id="recover_password" class="submit button" value="<?php _e('Get New Password', 'profilebuilder'); ?>" />
							<input name="action" type="hidden" id="action" value="recover_password" />
						</p><!-- .form-submit -->
						<?php wp_nonce_field('verify_true_password_recovery', 'password_recovery_nonce_field'); ?>
					</form><!-- #recover_password -->
	<?php
				}elseif ($messageNo == '5'){
					$recoverPasswordFilterArray['messageDisplay1'] = '
						<p class="warning">'.$message.'</p><!-- .warning -->';
					$recoverPasswordFilterArray['messageDisplay1'] = apply_filters('wppb_recover_password_displayed_message1', $recoverPasswordFilterArray['messageDisplay1']);
					echo $recoverPasswordFilterArray['messageDisplay1'];
				}else{
					//display success message
					$recoverPasswordFilterArray['messageDisplay2'] = '
						<p class="success">'.$message.'</p><!-- .success -->';
					$recoverPasswordFilterArray['messageDisplay2'] = apply_filters('wppb_recover_password_displayed_message2', $recoverPasswordFilterArray['messageDisplay2']);
					echo $recoverPasswordFilterArray['messageDisplay2'];
				}
			}
			/* use this action hook to add extra content after the password recovery form. */
			do_action( 'wppb_after_recover_password_fields' );
?>
	</div>
	
<?php
	$output = ob_get_contents();
    ob_end_clean();
		
	$recoverPasswordFilterArray = apply_filters('wppb_recover_password', $recoverPasswordFilterArray);
	
    return $output;
}
?>